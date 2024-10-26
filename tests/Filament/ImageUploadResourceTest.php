<?php

// Start - Edit these imports
use App\Filament\Resources\ImageUploadResource as Resource;
use App\Filament\Resources\ImageUploadResource\Pages\CreateImageUpload as CreateResource;
use App\Filament\Resources\ImageUploadResource\Pages\EditImageUpload as EditResource;
use App\Filament\Resources\ImageUploadResource\Pages\ListImageUploads as ListResource;
use App\Filament\Resources\ImageUploadResource\Widgets\StatsOverview;
use App\Models\ImageUpload as Model;
// End
use App\Models\User;
use Illuminate\Http\UploadedFile;

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

    it('can see stats overview widget', function () {
        livewire(ListResource::class)
            ->assertSeeLivewire(StatsOverview::class);
    });

    // Create Records

    it('can render create page', function () {
        $this->get(Resource::getUrl('create'))->assertSuccessful();
    });

    it('can create a record', function () {
        $newData = Model::factory()->make();

        Storage::fake();

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'image' => [
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
                'alt_text' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'title' => 'required',
                'alt_text' => 'required',
                'image' => 'required',
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
                'alt_text' => $record->alt_text,
                'user_id' => $record->user_id,
            ]);
    });

    it('can save data on edit form', function () {
        $record = Model::factory()->create();
        $newData = Model::factory()->make();

        Storage::fake();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->fillForm([
                ...$newData->toArray(),
                'image' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($record->refresh())
            ->title->toBe($newData->title)
            ->alt_text->toBe($newData->alt_text)
            ->user_id->toBe($newData->user_id);
    });

    it('can delete a record', function () {
        $record = Model::factory()->create();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\DeleteAction::class);

        $this->assertModelMissing($record);
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

    it('can create a record if user has enough space', function () {
        $newData = Model::factory()->make([
            'user_id' => $this->nonAdmin->id,
        ]);

        Storage::fake();

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'image' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Model::class, $newData->toArray());
    });

    it('can validate input', function () {
        $newData = Model::factory()->make([
            'user_id' => $this->nonAdmin->id,
        ]);

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'title' => null,
                'alt_text' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'title' => 'required',
                'alt_text' => 'required',
                'image' => 'required',
            ]);
    });

    it('prevents creation if user is out of space', function () {
        $this->nonAdmin->update([
            'image_uploaded_size' => (config('app-upload.limit') + 10),
        ]);

        $newData = Model::factory()->make([
            'user_id' => $this->nonAdmin->id,
        ]);

        livewire(CreateResource::class)
            ->fillForm([
                ...$newData->toArray(),
                'image' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
            ->call('create')
            ->assertHasFormErrors([
                'image',
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
                'alt_text' => $record->alt_text,
                'user_id' => $record->user_id,
            ]);
    });

    it('can save data on edit form', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);
        $newData = Model::factory()->make([
            'user_id' => $this->nonAdmin->id,
        ]);

        Storage::fake();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->fillForm([
                ...$newData->toArray(),
                'image' => [
                    UploadedFile::fake()->image('avatar.jpg'),
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($record->refresh())
            ->title->toBe($newData->title)
            ->alt_text->toBe($newData->alt_text)
            ->user_id->toBe($newData->user_id);
    });

    it('can delete a record', function () {
        $record = Model::factory()->create([
            'user_id' => $this->nonAdmin->id,
        ]);

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\DeleteAction::class);

        $this->assertModelMissing($record);
    });

    // Actions

    it('has copyImageUrl action on list page', function () {
        $record = Model::factory()
            ->for($this->nonAdmin)
            ->create();

        livewire(ListResource::class)
            ->assertTableActionVisible('copyImageUrl');
    });

    it('has copyImageUrl action on edit page', function () {
        $record = Model::factory()
            ->for($this->nonAdmin)
            ->create();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionExists('copyImageUrl');
    });

    it('has copyMarkdownUrl action on list page', function () {
        $record = Model::factory()
            ->for($this->nonAdmin)
            ->create();

        livewire(ListResource::class)
            ->assertTableActionVisible('copyMarkdownUrl');
    });

    it('has copyMarkdownUrl action on edit page', function () {
        $record = Model::factory()
            ->for($this->nonAdmin)
            ->create();

        livewire(EditResource::class, [
            'record' => $record->getRouteKey(),
        ])
            ->assertActionExists('copyMarkdownUrl');
    });
});
