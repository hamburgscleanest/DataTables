# Changelog

All Notable changes to `data-tables` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## NEXT

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.0.1

### Security
- LinkColumn: Opens external anchors using rel="noopener"

## 1.0.0

The first revision of the wiki has been released!\
https://github.com/hamburgscleanest/data-tables/wiki

### Added
- `Automatic Package Discovery` for Laravel versions 5.5.x and later
- Caching of the results
``` php       
     DataTable::model(TestModel::class, $columns, new SimpleCache($minutes));
     // or
     $dataTable->cache(new SimpleCache(1440));
```
- You can now change the pagination symbols
``` php       
     $paginator->pageSymbols(['first' => 'first page', 'last' => 'last page', 'next' => 'next', 'previous' => 'prev']);
```

## 0.9.2

### Fixed
- Search modes in the "FulltextSearch" class can now be set correctly

## 0.9.1

### Added
- LinkColumn formatter
``` php
/**
  * Every attribute of the model can be used to generate a link.
  * For example generating a link to the users profile on the "name" column.
  * Values in curly braces, e.g. "{id}" will be replaced by the model's value.
  */
$dataTable->formatColumn('name', new LinkColumn('/users/{id}')); // e.g. /users/1337
```

## 0.9.0

### Added
- Components can now be accessed directly via properties
``` php
// can be accessed via $dataTable->paginator
$dataTable->addComponent(new Paginator);
```
- ColumnFormatter for Icons -> IconColumn
- ColumnFormatter for Images -> ImageColumn

### Fixed
- Remembering the state does not work
- DataComponents could not access the defined relations (columns of relations like 'relation.id')

## 0.8.1

### Fixed
- SortableHeader: Fixed sorting bug..

## 0.8.0

### Added
- Related columns can be referenced now.
- Support for aggregates like "count", "max", "min", etc.
- Display custom HTML or a View if the dataset is empty.

### Fixed
- The page count is calculated correctly now.

### Removed
- You can no longer pass a closure to the render method. Use "noDataHtml()" or "noDataView()" instead.

## 0.7.0

initial beta version
