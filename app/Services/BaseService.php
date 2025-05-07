<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;

class BaseService
{
    public function saveOrUpdate(Model $model, array $data, int $id = null, bool $instance = false)
    {
        try {
            return $this->hasRelation($data, $model)
                ? $this->safeSave($model, $data, $id, $instance)
                : $this->normalSave($model, $data, $id, $instance);
        } catch (\Exception $e) {
            Log::error('Error in saveOrUpdate: ' . $e->getMessage());
            return false;
        }
    }

    public function delete(Model $model, $id)
    {
        try {
            return DB::transaction(function () use ($model, $id) {
                $record = $model::findOrFail($id);
                $this->deleteFilesInRecord($record);
                return $record->delete();
            });
        } catch (\Exception $e) {
            Log::error('Error in delete: ' . $e->getMessage());
            return false;
        }
    }

    public function restore($model, $id)
    {
        try {
            return $model::withTrashed()->findOrFail($id)->restore();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Error in restore: ' . $e->getMessage());
            return ['message' => 'An error occurred while restroring data.', 'status_code' => 500];
        }
    }

    public function showTrashed($model)
    {
        try {
            return $model::onlyTrashed()->get();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Error in showTrashed: ' . $e->getMessage());
            return ['message' => 'An error occurred while fetching trashed data.', 'status_code' => 500];
        }
    }

    public function deleteWithRelations(Model $model, $id)
    {
        try {
            DB::transaction(function () use ($model, $id) {
                $record = $model::with($this->getRelations(new $model))->findOrFail($id);
                $deleted = $record->forceDelete();

                if ($deleted) {
                    $this->deleteFilesInRecord($record);
                }

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Error in forceDelete: ' . $e->getMessage());
            return false;
        }
    }

    public function extractData($document)
    {
        $pdfPath = $document->getRealPath();

        try {
            $binaryPath = config('app.env') === 'local' ? 'C:\poppler\Library\bin\pdftotext.exe' : null;
            $text = Pdf::getText($pdfPath, $binaryPath);
            return $text;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteExistingFile($fileURL)
    {
        $storagePath = config('app.backend_url') . '/storage/';
        $filePath = str_replace($storagePath, '', $fileURL);

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    public function uploadFile($file, $model, $name)
    {
        try {
            $type = str_starts_with($file->getClientMimeType(), 'image/') ? 'images' : 'documents';
            $folder = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', class_basename($model)));
            $filename = $this->renameFile($file, $name) ?? $file->getClientOriginalName();
            $file->storeAs("public/{$type}/{$folder}", $filename);

            return url("storage/{$type}/{$folder}/{$filename}");
        } catch (\Exception $e) {
            Log::error('Failed to upload file: ' . $e->getMessage());
        }
    }

    private function getRelations(Model $model)
    {
        $relations = [];
        $reflection = new \ReflectionClass($model);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->class === get_class($model) && $method->getNumberOfParameters() === 0) {
                try {
                    $methodName = $method->getName();
                    $result = $model->$methodName();

                    if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                        $relations[] = $methodName;
                    }
                } catch (\Throwable $e) {
                    Log::error('Error in getRelations: ' . $e->getMessage());
                    return false;
                }
            }
        }

        return $relations;
    }

    private function deleteFilesInRecord($record)
    {
        foreach ($record->getAttributes() as $attribute => $value) {
            if ($this->isFileColumn($attribute) && $value) {
                $this->deleteFile($value);
            }
        }

        $relations = $record->getRelations();

        foreach ($relations as $relation => $related) {
            if ($related instanceof \Illuminate\Database\Eloquent\Model) {
                $this->deleteFilesInRecord($related);
            }

            if ($related instanceof \Illuminate\Database\Eloquent\Collection) {
                $related->each(function ($relatedModel) {
                    $this->deleteFilesInRecord($relatedModel);
                });
            }
        }
    }

    private function isFileColumn($attribute)
    {
        return Str::contains($attribute, ['file', 'image', 'document', 'icon', 'logo']);
    }

    private function normalSave(Model $model, array $data, $id, $instance)
    {
        try {
            $result = $this->executeSave($model, $data, $id);

            return $instance ? $result : $result instanceof Model;
        } catch (\Exception $e) {
            Log::error('Error in normalSave: ' . $e->getMessage());
            return false;
        }
    }

    private function safeSave(Model $model, array $data, $id, $instance)
    {
        try {
            DB::beginTransaction();
            $result = $this->executeSave($model, $data, $id, true); // syncRelation = true
            $result instanceof Model ? DB::commit() : DB::rollBack();

            return $instance ? $result : $result instanceof Model;
        } catch (\Exception $e) {
            Log::error('Error in safe saving: ' . $e->getMessage());
            return false;
        }
    }

    private function executeSave(Model $model, array $data, $id = null, $syncRelations = false)
    {
        try {
            $instance = $this->createOrUpdateInstance($model, $data, $id);

            if ($syncRelations) {
                $relationData = $this->filterRelationalData($data, $model);
                if (!empty($relationData)) {
                    $this->syncRelations($instance, $relationData);
                }
            }

            return $instance;
        } catch (\Exception $e) {
            Log::error('Error in executeSave: ' . $e->getMessage());
            return false;
        }
    }

    private function createOrUpdateInstance(Model $model, array $data, $id = null)
    {
        try {
            $existingModel = $id ? $model::findOrFail($id) : $model;
            $filteredData = $this->filterNonRelationalData($data, $existingModel);
            $data = $this->uploadFileIfExist($filteredData, $existingModel);
            return $id ? tap($existingModel)->update($data) : $model::create($data);
        } catch (\Exception $e) {
            Log::error('Error in createOrUpdateInstance: ' . $e->getMessage());
            throw $e;
        }
    }

    private function hasRelation(array $data, Model $model)
    {
        return !empty($this->filterRelationalData($data, $model));
    }

    private function filterNonRelationalData(array $data, $model)
    {
        return collect($data)->filter(fn($value) => !is_array($value))->toArray();
    }

    private function uploadFileIfExist(array $data, $model)
    {
        $dataWithFiles = $this->filterDataWithFiles($data);

        if ($dataWithFiles) {
            foreach ($dataWithFiles as $key => $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    if ($model && isset($model->$key) && $model->$key) {
                        $existingFile = $model->$key;
                        $this->deleteFile($existingFile);
                    }

                    $name = strtolower($key);
                    $data[$key] = $this->uploadFile($file, $model, $name);
                }
            }
        }

        return $data;
    }

    private function filterRelationalData(array $data, Model $model)
    {
        return collect($data)->filter(fn($value, $key) => is_array($value) && method_exists($model, Str::camel($key)))
            ->mapWithKeys(fn($value, $key) => [Str::camel($key) => $value])
            ->toArray();
    }

    private function syncRelations($instance, array $data)
    {
        info('Relations to sync: ' . implode(', ', array_keys($data)));

        foreach ($data as $relation => $relationData) {
            if (!method_exists($instance, $relation)) {
                throw new \Exception("No method for relation: $relation found on model.");
            }

            $relationInstance = $instance->$relation();

            match (true) {
                $relationInstance instanceof MorphMany => $this->syncMorphMany($relationInstance, $relationData),
                $relationInstance instanceof HasMany => $this->syncHasMany($instance, $relationInstance, $relationData),
                $relationInstance instanceof HasOne => $relationInstance->updateOrCreate([], $relationData),
                $relationInstance instanceof BelongsToMany => $relationInstance->sync($relationData),
                default => throw new \Exception("Unexpected relation type for $relation: " . get_class($relationInstance))
            };
        }
    }

    private function syncMorphMany(MorphMany $relationInstance, array $relationData)
    {
        $existingIds = $relationInstance->pluck('id')->toArray();

        foreach ($relationData as $data) {
            if (isset($data['id']) && in_array($data['id'], $existingIds)) {
                $relationInstance->find($data['id'])->update($data);
            } else {
                $relationInstance->create($data);
            }
        }

        $newIds = array_column($relationData, 'id');

        $idsToDelete = array_diff($existingIds, $newIds);

        if (!empty($idsToDelete)) {
            $relationInstance->whereIn('id', $idsToDelete)->delete();
        }
    }

    private function syncHasMany($model, $relationInstance, array $relationData)
    {
        $this->deleteOldSyncData($relationInstance);
        $syncData = $this->createNewSyncData($relationData, $model);

        if (!empty($syncData)) {
            $relationInstance->createMany($syncData);
        }
    }

    private function deleteOldSyncData($relationInstance)
    {
        $documents = $relationInstance->get();
        $documents->each(function ($existing) {
            collect($existing->getAttributes())
                ->filter(fn($file) => $this->isFilePath($file))
                ->each(fn($file) => $this->deleteFile($file));

            $existing->delete();
        });
    }

    private function isFilePath($file)
    {
        return is_string($file) && (str_contains($file, 'storage/') || str_contains($file, 'documents/'));
    }

    private function deleteFile($filePath)
    {
        $storagePath = str_replace(url('/storage'), 'public', $filePath);

        if (Storage::exists($storagePath)) {
            Storage::delete($storagePath);
        }
    }

    private function createNewSyncData($relationData, $model)
    {
        return collect($relationData)->map(fn($data) => $this->processRelationData($data, $model))->toArray();
    }

    private function processRelationData($data, $model)
    {
        return collect($data)->mapWithKeys(function ($value, $key) use ($data, $model) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $_userName = '_' . $model?->full_name;
                $fileNamePrefix = isset($data['document_type']) ? $data['document_type'] .  $_userName : $key . $_userName;

                return [$key => $this->uploadFile($value, $model, $fileNamePrefix)];
            }

            return [$key => $value];
        })->toArray();
    }

    private function filterDataWithFiles(array $data)
    {
        return array_filter($data, fn($value) => $value instanceof \Illuminate\Http\UploadedFile);
    }

    private function renameFile($file, $name)
    {
        $prefix = strtolower(str_replace(' ', '_', $name));
        $uniqueId = substr(str_replace('-', '', Str::uuid()->toString()), 0, 8);

        return $prefix . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();
    }
}
