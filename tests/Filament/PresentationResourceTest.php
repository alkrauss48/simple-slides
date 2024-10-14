<?php

// Start - Edit these imports
use App\Filament\Resources\PresentationResource as Resource;
use App\Filament\Resources\PresentationResource\Pages\CreatePresentation as CreateResource;
use App\Filament\Resources\PresentationResource\Pages\EditPresentation as EditResource;
use App\Filament\Resources\PresentationResource\Pages\ListPresentations as ListResource;
use App\Jobs\GenerateThumbnail;
use App\Models\Presentation as Model;
// End
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
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

        // The factory generates a random historical created_at, but we don't
        // want that when creating a test record in Filament.
        unset($newData['created_at']);

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'thumbnail' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
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
            ->fillForm([
                ...$newData->toArray(),
                'thumbnail' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
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

        // The factory generates a random historical created_at, but we don't
        // want that when creating a test record in Filament.
        unset($newData['created_at']);

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'thumbnail' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
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
                'image' => null, // Optional field
            ])
            ->call('create')
            ->assertHasNoFormErrors([
                'thumbnail',
            ])
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
            ->fillForm([
                ...$newData->toArray(),
                'thumbnail' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
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

    it('can dispatch a thumbnail generation job', function () {
        Queue::fake();

        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
            'is_published' => true,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction('Generate Thumbnail');

        Queue::assertPushed(GenerateThumbnail::class);
    });

    it('will not dispatch a thumbnail generation job for unpublished presentation', function () {
        Queue::fake();

        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
            'is_published' => false,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction('Generate Thumbnail');

        Queue::assertNotPushed(GenerateThumbnail::class);
    });

    it('can view the presentation', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionHasUrl('view', route('presentations.show', [
                'user' => $record->user->username,
                'slug' => $record->slug,
            ]))
            ->assertActionShouldOpenUrlInNewTab('view');
    });

    it('can copy the share url of a published presentation', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
            'is_published' => true,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionEnabled('copyShareUrl');
    });

    it('can not copy the share url of a draft presentation', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
            'is_published' => false,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionDisabled('copyShareUrl');
    });
});
