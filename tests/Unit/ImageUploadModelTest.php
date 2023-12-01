<?php

use App\Models\ImageUpload;
use Illuminate\Http\UploadedFile;

describe('users with image uploads', function () {
    beforeEach(function () {
        $this->imageUpload = ImageUpload::factory()
            ->create([
                'alt_text' => 'Foo',
            ]);

        $this->imageUpload
            ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
            ->toMediaCollection('image');
    });

    test('markdownUrl attribute returns properly', function () {
        $imagePath = $this->imageUpload->getFirstMediaUrl('image');

        expect($imagePath)->not->toBeNull();

        $expected = "![Foo]($imagePath)";

        expect($this->imageUpload)
            ->markdownUrl->toBe($expected);
    });
});
