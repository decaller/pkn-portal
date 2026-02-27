<?php

namespace App\Filament\User\Resources\Events\Pages;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\User\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('register')
                ->label('Register for this event')
                ->icon('heroicon-o-ticket')
                ->color('success')
                ->visible(fn (): bool => $this->record->allow_registration && $this->record->event_date >= now()->toDateString())
                ->url(fn (): string => EventRegistrationResource::getUrl('create', [
                    'event_id' => $this->record->getKey(),
                ])),

            Action::make('submitSurvey')
                ->label('Submit Survey')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->form(function () {
                    $template = $this->record->surveyTemplate;
                    if (! $template || empty($template->questions)) {
                        return [];
                    }

                    $fields = [];
                    foreach ($template->questions as $index => $q) {
                        $name = 'question_'.$index;
                        $label = $q['question_text'] ?? 'Question';
                        $type = $q['type'] ?? 'text';

                        if ($type === 'text') {
                            $fields[] = \Filament\Forms\Components\TextInput::make($name)->label($label)->required();
                        } elseif ($type === 'textarea') {
                            $fields[] = \Filament\Forms\Components\Textarea::make($name)->label($label)->required();
                        } elseif ($type === 'rating') {
                            $fields[] = \Filament\Forms\Components\Select::make($name)->label($label)
                                ->options([1 => '1 Star', 2 => '2 Stars', 3 => '3 Stars', 4 => '4 Stars', 5 => '5 Stars'])->required();
                        }
                    }

                    return $fields;
                })
                ->action(function (array $data) {
                    $template = $this->record->surveyTemplate;
                    if (! $template) {
                        return;
                    }

                    $answers = [];
                    foreach ($template->questions as $index => $q) {
                        $answers[$q['question_text']] = $data['question_'.$index] ?? null;
                    }

                    \App\Models\SurveyResponse::updateOrCreate(
                        [
                            'survey_template_id' => $template->id,
                            'event_id' => $this->record->id,
                            'user_id' => auth()->id(),
                        ],
                        [
                            'answers' => $answers,
                        ]
                    );

                    \Filament\Notifications\Notification::make()
                        ->title('Survey submitted')
                        ->success()
                        ->send();
                })
                ->visible(function (): bool {
                    if (! $this->record->survey_template_id) {
                        return false;
                    }

                    return $this->record->registrations()->where('booker_user_id', auth()->id())->exists();
                }),

            Action::make('addTestimonial')
                ->label('Add Testimonial')
                ->icon('heroicon-o-star')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Textarea::make('content')
                        ->required()
                        ->label('Your Feedback')
                        ->rows(4),
                    \Filament\Forms\Components\ToggleButtons::make('rating')
                        ->options([
                            1 => '1 Star',
                            2 => '2 Stars',
                            3 => '3 Stars',
                            4 => '4 Stars',
                            5 => '5 Stars',
                        ])
                        ->inline()
                        ->required()
                        ->default(5),
                ])
                ->action(function (array $data) {
                    \App\Models\Testimonial::create([
                        'event_id' => $this->record->id,
                        'user_id' => auth()->id(),
                        'content' => $data['content'],
                        'rating' => $data['rating'],
                        'is_approved' => false,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Testimonial submitted')
                        ->body('Thank you for your feedback! It will be reviewed by an administrator.')
                        ->success()
                        ->send();
                })
                ->visible(function (): bool {
                    return $this->record->registrations()->where('booker_user_id', auth()->id())->exists();
                }),
        ];
    }
}
