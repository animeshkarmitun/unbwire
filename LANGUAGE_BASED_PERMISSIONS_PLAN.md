# Language-Based Permissions & Separate Create Options Plan

## Overview
This plan implements separate create options for Bangla (bn) and English (en) for all modules, with language-specific permissions and updated localization.

## Architecture

### 1. Permission Structure
Each module will have language-specific permissions:
- `{module} create en` - Create English content
- `{module} create bn` - Create Bangla content
- `{module} update en` - Update English content
- `{module} update bn` - Update Bangla content
- `{module} delete en` - Delete English content
- `{module} delete bn` - Delete Bangla content

### 2. Routes Structure
- Current: `/admin/news/create`
- New: 
  - `/admin/news/create/en` - Create English news
  - `/admin/news/create/bn` - Create Bangla news

### 3. Controller Methods
- `create($lang)` - Accept language parameter
- `store(Request $request, $lang)` - Store with language
- `edit($id, $lang)` - Edit with language context
- `update(Request $request, $id, $lang)` - Update with language

### 4. UI Changes
- Separate "Create English" and "Create Bangla" buttons
- Language badges/indicators on list views
- Language filter in index pages

## Modules to Update

1. **News**
   - Create routes: `/admin/news/create/en`, `/admin/news/create/bn`
   - Permissions: `news create en`, `news create bn`, `news update en`, `news update bn`, `news delete en`, `news delete bn`

2. **Category**
   - Create routes: `/admin/category/create/en`, `/admin/category/create/bn`
   - Permissions: `category create en`, `category create bn`, etc.

3. **Author** (if language-specific)
   - May not need language separation as authors are shared

4. **Pages** (About, Contact)
   - Language-specific versions

## Implementation Steps

### Phase 1: Permissions
1. Update permission seeder
2. Add language-specific permissions
3. Update permission checks in controllers

### Phase 2: Routes
1. Update route definitions
2. Add language parameter to routes
3. Update route names

### Phase 3: Controllers
1. Update create methods to accept language
2. Update store methods
3. Update edit/update methods
4. Add language validation

### Phase 4: Views
1. Update index pages with language buttons
2. Update create/edit forms
3. Add language indicators
4. Update sidebar navigation

### Phase 5: Localization
1. Add new translation keys
2. Update existing translations
3. Add language-specific labels

## Benefits
- Better access control per language
- Clearer UI with language-specific actions
- Easier content management per language
- Better permission granularity

