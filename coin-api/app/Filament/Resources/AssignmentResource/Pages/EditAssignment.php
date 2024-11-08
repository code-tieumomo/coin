<?php

namespace App\Filament\Resources\AssignmentResource\Pages;

use App\Filament\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\Subnet;
use App\Models\User;
use DB;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAssignment extends EditRecord
{
    protected static string $resource = AssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $subnets = Subnet::whereHas('assignments', fn($query) => $query->where('assignment_id', $data['id']))
            ->with(['assignments' => fn($query) => $query->where('assignment_id', $data['id'])])
            ->get()
            ->map(function (Subnet $subnet) {
                return [
                    'subnet' => $subnet->id,
                    'weight' => $subnet->assignments->first()->pivot->weight,
                    'needed' => $subnet->assignments->first()->pivot->needed,
                ];
            })
            ->toArray();
        $data['subnets'] = $subnets;

        if (!$data['is_public']) {
            $users = User::select('id', 'name')->whereHas('assignments', fn($query) => $query->where('assignment_id', $data['id']))
                ->with(['assignments' => fn($query) => $query->where('assignment_id', $data['id'])])
                ->get()
                ->map(function (User $user) {
                    return $user->id;
                })
                ->toArray();
            $data['users'] = $users;
        } else {
            $data['users'] = [];
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            $record->update([
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
                $record->subnets()->sync($subnets);
            }

            // Attach users
            $isPublic = (bool) $data['is_public'];
            $users = $isPublic
                ? User::select('id')->get()->pluck('id')->values()->toArray()
                : collect($data['users'])->map(fn($item) => intval($item))->values()->toArray();
            if (!empty($users)) {
                $record->users()->sync($users);
                $record->subnets->each(function (Subnet $subnet) use ($users) {
                    $subnet->users()->attach($users);
                });
            }

            DB::commit();

            return $record;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
