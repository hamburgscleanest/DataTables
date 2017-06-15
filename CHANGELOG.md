# Changelog

All Notable changes to `data-tables` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 0.9.0

### Added
- Components can now be accessed directly via properties
``` php
// can be accessed via $dataTable->paginator
$dataTable->addComponent(new Pagiantor);
```

### Deprecated
- Nothing

### Fixed
- Remembering the state does not work
- DataComponents need to be able to access the relations

### Removed
- Nothing

### Security
- Nothing

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
