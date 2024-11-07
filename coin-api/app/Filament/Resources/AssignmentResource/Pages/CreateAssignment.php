<?php

namespace App\Filament\Resources\AssignmentResource\Pages;

use App\Filament\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\User;
use DB;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Log;

class CreateAssignment extends CreateRecord
{
    protected static string $resource = AssignmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();
        try {
            // Create assignment
            $assignment = Assignment::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_public' => $data['is_public'],
            ]);

            // Attach subnets
            $subnets = $data['subnets'] ?? [];
            $subnets = collect($subnets)->mapWithKeys(function (array $item, int $key) {
                return [
                    (int) $item['subnet'] => [
                        'weight' => $item['weight'],
                        'needed' => $item['needed'],
                    ]
                ];
            })->toArray();
            if (!empty($subnets)) {
                $assignment->subnets()->sync($subnets);
            }

            // Attach users
            $isPublic = (bool) $data['is_public'];
            $users = $isPublic
                ? User::select('id')->get()->pluck('id')->values()->toArray()
                : collect($data['users'])->map(fn($item) => intval($item))->values()->toArray();
            if (!empty($users)) {
                $assignment->users()->sync($users);
            }

            DB::commit();

            return $assignment;
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), $e->getTrace() ?? []);
            DB::rollBack();
            throw $e;
        }
    }
}
