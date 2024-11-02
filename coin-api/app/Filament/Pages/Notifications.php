<?php

namespace App\Filament\Pages;

use App\Events\PrivateNotification;
use App\Models\User;
use DB;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\Rule;
use Log;

class Notifications extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static string $view = 'filament.pages.notifications';

    public ?array $generalNotificationFormData = [];

    public ?array $userSuggestions = [];

    public function mount(): void
    {
        $this->fillForms();

        $this->userSuggestions = User::all()->map(function (User $user): string {
            return '[' . str_pad($user->id, 4, '0', STR_PAD_LEFT) . '] ' . $user->name;
        })->toArray();
    }

    protected function getForms(): array
    {
        return [
            'generalNotificationForm',
            // 'privateNotificationForm',
        ];
    }

    protected function fillForms(): void
    {
        $this->generalNotificationForm->fill();
        // $this->privateNotificationForm->fill();
    }

    public function generalNotificationForm(Form $form): Form
    {
        return $form
            ->statePath('generalNotificationFormData')
            ->schema([
                Section::make('Notification')
                    ->description('Send a notification to user.')
                    ->columns([
                        'lg' => 2,
                    ])
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'info' => 'Info',
                                'success' => 'Success',
                                'warning' => 'Warning',
                                'error' => 'Error',
                            ])
                            ->required(),
                        Textarea::make('content')
                            ->label('Content')
                            ->required()
                            ->rows(5)
                            ->columnSpan(2),
                        Toggle::make('is_private')
                            ->label('Private')
                            ->onIcon('heroicon-m-lock-closed')
                            ->offIcon('heroicon-m-lock-open')
                            ->live()
                            ->columnSpanFull(),
                        TagsInput::make('users')
                            ->label('Users')
                            ->placeholder('Select users to send the notification to.')
                            ->suggestions($this->userSuggestions)
                            ->hidden(fn(Get $get): bool => !$get('is_private'))
                            ->nestedRecursiveRules([
                                Rule::in($this->userSuggestions),
                            ])
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public function sendGeneralNotification()
    {
        $data = $this->generalNotificationForm->getState();

        DB::beginTransaction();
        try {
            $isPrivate = (bool) $data['is_private'];
            if ($isPrivate) {
                $pickedUsers = collect($data['users'])->map(function (string $user): int {
                    preg_match('/\[(\d+)\]/', $user, $matches);
                    return (int) $matches[1];
                })->toArray();
                $users = User::whereIn('id', $pickedUsers)->get();
            } else {
                $users = User::all();
            }
            foreach ($users as $user) {
                $notification = \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'type' => $data['type'],
                    'content' => $data['content'],
                ]);
                PrivateNotification::dispatch($notification);
            }
            DB::commit();
            Notification::make()
                ->title('The notification has been sent successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());
            Notification::make()
                ->title('An error occurred while sending the notification.')
                ->danger()
                ->send();
        }
    }
}
