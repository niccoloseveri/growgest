<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\PipelineStage;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class CustomersKanbanBoard extends KanbanBoard
{
    protected static string $model = Customer::class;
    protected static string $statusEnum = PipelineStage::class;
    protected static ?int $navigationSort = 20;


    protected function records(): Collection
    {
        return Customer::where('pipeline_stage_id')->get();
    }
    protected function statuses(): Collection
    {
        return PipelineStage::all()
            ->sortBy('position')
            ->map(function (PipelineStage $stage) {
                return [
                    'id' => $stage->id,
                    'title' => $stage->name,
                ];
            });
    }
}
