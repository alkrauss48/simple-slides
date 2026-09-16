---
paths:
  - app/Filament/Resources/**
---

# Filament

## Resources follow the v5 generated file layout
Resources live at `Resources/{PluralModel}/{Model}Resource.php`, with `Pages/`,
`Schemas/`, `Tables/`, and `RelationManagers/` as sibling directories. Do not
revert to the flat v3 shape (`XxxResource.php` plus an `XxxResource/` folder).

Form and table bodies are standalone classes, not inline on the resource:

-   `Schemas/{Model}Form.php` — `public static function configure(Schema $schema): Schema`
-   `Tables/{PluralModel}Table.php` — `public static function configure(Table $table): Table`
-   `Schemas/{Model}Infolist.php` — only when the resource has a view page

The table basename is **plural** (`UsersTable`, `PresentationsTable`), the form
basename is singular. These classes deliberately have no parent class or
interface. The resource just delegates:

    public static function form(Schema $schema): Schema
    {
        return PresentationForm::configure($schema);
    }

Relation managers keep `form()`/`table()` inline as *instance* methods — that is
the generator's default, so don't split them into Schemas/Tables classes.

This matches what `make:filament-resource` emits because `config/filament.php`
sets no `file_generation.flags`. Adding `embedded_panel_resource_schemas`,
`embedded_panel_resource_tables`, or `panel_resource_classes_outside_directories`
there would change the generator's output and contradict this layout.
