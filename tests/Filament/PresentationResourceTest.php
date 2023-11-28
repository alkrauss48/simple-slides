<?php

// Start - Edit these imports
use App\Filament\Resources\PresentationResource as Resource;
use App\Filament\Resources\PresentationResource\Pages\CreatePresentation as CreateResource;
use App\Filament\Resources\PresentationResource\Pages\EditPresentation as EditResource;
use App\Filament\Resources\PresentationResource\Pages\ListPresentations as ListResource;
use App\Models\Presentation as Model;
// End
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create([
        'is_admin' => true,
    ]);

});

describe('admin users', function () {
    beforeEach(function () {
        $this->actingAs($this->user);
    });

    // List Records

    it('can render index page', function () {
        $this->get(Resource::getUrl('index'))->assertSuccessful();
    });

    it('can list records', function () {
        $records = Model::factory()
            ->count(10)
            ->create();

        livewire(ListResource::class)
            ->assertCanSeeTableRecords($records);
    });

    // Create Records

    it('can render create page', function () {
        $this->get(Resource::getUrl('create'))->assertSuccessful();
    });

    it('can create a record', function () {
        $newData = Model::factory()->make();

        livewire(CreateResource::class)
            ->fillForm($newData->toArray())
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Model::class, $newData->toArray());
    });

    it('can validate input', function () {
        $newData = Model::factory()->make();

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'title' => null,
                'content' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'title' => 'required',
                'content' => 'required',
            ]);
    });

    // Edit Records

    it('can render edit page', function () {
        $record = Model::factory()->create();

        $this->get(Resource::getUrl('edit', [
            'record' => $record->getRouteKey(),
        ]))->assertSuccessful();
    });

    it('can retrieve data on edit form', function () {
        $record = Model::factory()->create();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertFormSet([
                'title' => $record->title,
                'description' => $record->description,
                'content' => $record->content,
                'is_published' => $record->is_published,
                'user_id' => $record->user_id,
            ]);
    });

    it('can save data on edit form', function () {
        $record = Model::factory()->create();
        $newData = Model::factory()->make();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->fillForm($newData->toArray())
            ->call('save')
            ->assertHasNoFormErrors();

        expect($record->refresh())
            ->title->toBe($newData->title)
            ->description->toBe($newData->description)
            ->content->toBe($newData->content)
            ->user_id->toBe($newData->user_id)
            ->is_published->toBe($newData->is_published);
    });

    it('can soft delete record', function () {
        $record = Model::factory()->create();

        expect($record)
            ->deleted_at->toBe(null);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\DeleteAction::class);

        // $this->assertModelMissing($record);

        expect($record->refresh())
            ->deleted_at->not->toBe(null);
    });

    it('force delete is not an option if the record is not soft deleted', function () {
        $record = Model::factory()->create();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionHidden(\Filament\Actions\ForceDeleteAction::class);
    });

    it('restore is not an option if the record is not soft deleted', function () {
        $record = Model::factory()->create();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionHidden(\Filament\Actions\RestoreAction::class);
    });

    it('can force delete a soft-deleted record', function () {
        $record = Model::factory()->create();

        $record->delete();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\ForceDeleteAction::class);

        $this->assertModelMissing($record);
    });

    it('can restore a soft-deleted record', function () {
        $record = Model::factory()->create();

        $record->delete();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\RestoreAction::class);

        expect($record->refresh())
            ->deleted_at->toBe(null);
    });
});

describe('non-admin users', function () {
    beforeEach(function () {
        $this->nonAdmin = User::factory()->create();

        $this->actingAs($this->nonAdmin);
    });

    // List Records

    it('can render index page', function () {
        $this->get(Resource::getUrl('index'))->assertSuccessful();
    });

    it('can list only records that belong to them', function () {
        $records = Model::factory()
            ->count(5)
            ->create([
                'user_id' => $this->nonAdmin->id,
            ]);

        $adminRecords = Model::factory()
            ->count(5)
            ->create([
                'user_id' => $this->user->id,
            ]);

        livewire(ListResource::class)
            ->assertCanSeeTableRecords($records)
            ->assertCanNotSeeTableRecords($adminRecords);
    });

    // Create Records

    it('can render create page', function () {
        $this->get(Resource::getUrl('create'))->assertSuccessful();
    });

    it('can create a record', function () {
        $newData = Model::factory()->make();

        livewire(CreateResource::class)
            ->fillForm($newData->toArray())
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Model::class, $newData->toArray());
    });

    it('can validate input', function () {
        $newData = Model::factory()->make();

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'title' => null,
                'content' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'title' => 'required',
                'content' => 'required',
            ]);
    });

    // Edit Records

    it('can render edit page', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        $this->get(Resource::getUrl('edit', [
            'record' => $record->getRouteKey(),
        ]))->assertSuccessful();
    });

    it('can not render edit page of a record from another user', function () {
        $record = Model::factory()->create();

        $this->get(Resource::getUrl('edit', [
            'record' => $record->getRouteKey(),
        ]))->assertNotFound();
    });

    it('can retrieve data on edit form', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertFormSet([
                'title' => $record->title,
                'description' => $record->description,
                'content' => $record->content,
                'is_published' => $record->is_published,
                'user_id' => $record->user_id,
            ]);
    });

    it('can save data on edit form', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);
        $newData = Model::factory()->make();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->fillForm($newData->toArray())
            ->call('save')
            ->assertHasNoFormErrors();

        expect($record->refresh())
            ->title->toBe($newData->title)
            ->description->toBe($newData->description)
            ->content->toBe($newData->content)
            ->is_published->toBe($newData->is_published);
    });

    it('can soft delete record created by the user', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        expect($record)
            ->deleted_at->toBe(null);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\DeleteAction::class);

        expect($record->refresh())
            ->deleted_at->not->toBe(null);
    });

    it('force delete is not an option if the record is not soft deleted', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionHidden(\Filament\Actions\ForceDeleteAction::class);
    });

    it('restore is not an option if the record is not soft deleted', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionHidden(\Filament\Actions\RestoreAction::class);
    });

    it('can force delete a soft-deleted record created by the user', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        $record->delete();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\ForceDeleteAction::class);

        $this->assertModelMissing($record);
    });

    it('can restore a soft-deleted record created by the user', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        $record->delete();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\RestoreAction::class);

        expect($record->refresh())
            ->deleted_at->toBe(null);
    });
});
