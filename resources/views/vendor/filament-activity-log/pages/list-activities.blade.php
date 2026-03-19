@php
    use \Illuminate\Support\Js;
@endphp
<x-filament-panels::page>
    <div class="space-y-6">
        @foreach($this->getActivities() as $activityItem)

            @php
                /* @var \Spatie\Activitylog\Models\Activity $activityItem */
                $changes = $activityItem->getChangesAttribute();
            @endphp

            <div @class([
                'p-2 space-y-2 bg-white rounded-xl shadow',
                'dark:border-gray-600 dark:bg-gray-800',
            ])>
                <div class="px-3 py-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            @if ($activityItem->causer)
                                <x-filament-panels::avatar.user :user="$activityItem->causer" class="!w-10 !h-10"/>
                            @endif
                            <div class="flex flex-col text-start">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $activityItem->causer?->name ?? 'System' }}</span>
                                    <span @class([
                                        'px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wider shadow-sm',
                                        'bg-success-500/10 text-success-600 dark:text-success-400' => $activityItem->event === 'created',
                                        'bg-warning-500/10 text-warning-600 dark:text-warning-400' => $activityItem->event === 'updated',
                                        'bg-danger-500/10 text-danger-600 dark:text-danger-400' => $activityItem->event === 'deleted',
                                        'bg-gray-500/10 text-gray-600 dark:text-gray-400' => !in_array($activityItem->event, ['created', 'updated', 'deleted']),
                                    ])>
                                        {{ $activityItem->event }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    {{ $activityItem->created_at->translatedFormat('d M Y, H:i:s') }}
                                    
                                    @if($activityItem->subject && $activityItem->subject_id != $this->record->getKey())
                                        <span class="mx-1">•</span>
                                        <span class="italic">
                                            {{ strtolower(class_basename($activityItem->subject_type)) }}:
                                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ $activityItem->subject->name ?? $activityItem->subject->title ?? $activityItem->subject->id ?? '#' . $activityItem->subject_id }}
                                            </span>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($changes->isNotEmpty())
                    <div class="border-t border-gray-100 dark:border-gray-700/50">
                        <table class="fi-ta-table w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/20">
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600 dark:text-gray-400 w-1/4">
                                        {{ __('Field') }}
                                    </th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600 dark:text-gray-400 w-3/8">
                                        {{ __('Old Value') }}
                                    </th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600 dark:text-gray-400 w-3/8">
                                        {{ __('New Value') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @foreach (data_get($changes, 'attributes', []) as $field => $change)
                                    @php
                                        $oldValue = isset($changes['old'][$field]) ? $changes['old'][$field] : '';
                                        $newValue = isset($changes['attributes'][$field]) ? $changes['attributes'][$field] : '';
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">
                                            {{ str( $this->getFieldLabel($field) )->headline() }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400 italic">
                                            @if(is_array($oldValue))
                                                <pre class="text-[10px] leading-tight font-mono p-1 bg-gray-100 dark:bg-gray-900 rounded">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @else
                                                {{ $oldValue ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-white font-medium">
                                            @if (is_bool($newValue))
                                                <span @class(['px-2 py-0.5 rounded text-[10px] font-bold', 'bg-success-500/10 text-success-600' => $newValue, 'bg-danger-500/10 text-danger-600' => !$newValue])>
                                                    {{ $newValue ? 'TRUE' : 'FALSE' }}
                                                </span>
                                            @elseif(is_array($newValue))
                                                <pre class="text-[10px] leading-tight font-mono p-1 bg-gray-100 dark:bg-gray-900 rounded">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @else
                                                {{ $newValue ?? '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-4 py-3 bg-gray-50/50 dark:bg-gray-900/20 border-t border-gray-100 dark:border-gray-700/50 text-[10px] text-gray-400 italic">
                        No detailed attribute changes recorded for this entry.
                    </div>
                @endif
            </div>
        @endforeach

        <x-filament::pagination
            currentPageOptionProperty="recordsPerPage"
            :page-options="$this->getRecordsPerPageSelectOptions()"
            :paginator="$this->getActivities()"
        />
    </div>
</x-filament-panels::page>
