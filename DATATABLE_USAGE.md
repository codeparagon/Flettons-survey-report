# Professional DataTable Component Usage Guide

## Overview

A professional, reusable DataTable component has been created that perfectly blends with your SurvAI theme (Dark teal #1a202c with #c1ec4a accent). The DataTable includes:

- ✅ Professional styling matching your brand colors
- ✅ Search functionality
- ✅ Pagination controls
- ✅ Sorting capability
- ✅ Responsive design
- ✅ Export buttons (optional)
- ✅ Customizable columns and data

## Features

- **Brand Colors**: Uses #1a202c for dark backgrounds and #c1ec4a for accents
- **Professional Styling**: Rounded corners, proper spacing, and hover effects
- **Search**: Built-in search functionality
- **Pagination**: Configurable items per page
- **Sorting**: Click column headers to sort
- **Responsive**: Works on all screen sizes
- **Export**: Optional Excel, PDF, and Print buttons

## Basic Usage

### 1. Simple DataTable

```blade
<x-datatable id="myTable" :columns="['ID', 'Name', 'Email', 'Status']">
    <tr>
        <td>1</td>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td><span class="badge badge-success">Active</span></td>
    </tr>
    <tr>
        <td>2</td>
        <td>Jane Smith</td>
        <td>jane@example.com</td>
        <td><span class="badge badge-success">Active</span></td>
    </tr>
</x-datatable>
```

### 2. With Loop Data

```blade
<x-datatable id="usersTable" :columns="['ID', 'Name', 'Email', 'Role', 'Actions']">
    @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->display_name }}</td>
            <td>
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </td>
        </tr>
    @endforeach
</x-datatable>
```

### 3. With Card Header (Recommended)

```blade
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Users</h5>
            <a href="{{ route('users.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <x-datatable id="usersTable" :columns="['ID', 'Name', 'Email', 'Role', 'Actions']">
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->display_name }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-datatable>
    </div>
</div>
```

## Advanced Options

### With Export Buttons

```blade
<x-datatable 
    id="exportsTable" 
    :columns="['ID', 'Name', 'Email', 'Status']"
    :exportButtons="true">
    <!-- Your table rows -->
</x-datatable>
```

### Disable Search or Pagination

```blade
<x-datatable 
    id="simpleTable" 
    :columns="['ID', 'Name']"
    :search="false"
    :paging="false">
    <!-- Your table rows -->
</x-datatable>
```

### Custom Items Per Page

The default options are: 10, 25, 50, 100, All

### No Data Message

When there's no data, the DataTable automatically shows "No records found".

## All Available Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `id` | string | 'datatable' | Unique ID for the table |
| `columns` | array | [] | Column headers |
| `search` | boolean | true | Enable/disable search |
| `length` | boolean | true | Show items per page selector |
| `paging` | boolean | true | Enable pagination |
| `ordering` | boolean | true | Enable column sorting |
| `info` | boolean | true | Show info text |
| `responsive` | boolean | true | Enable responsive mode |
| `buttons` | boolean | false | Show custom buttons |
| `exportButtons` | boolean | false | Show export buttons |

## Styling Guidelines

### Brand Colors
- **Primary Background**: #1a202c (Dark teal)
- **Accent Color**: #c1ec4a (Lime green)
- **Text Color**: #1a202c
- **Hover Background**: #f9fafb

### Buttons
The DataTable buttons automatically use your theme colors:
- Primary buttons: #C1EC4A background with #1A202C text
- Disabled buttons: 50% opacity
- Hover effects: Slightly darker shade

### Badges
Example badge usage:
```blade
<span class="badge badge-success">Active</span>
<span class="badge badge-secondary">Inactive</span>
```

## Example: Complete Users Management Page

```blade
@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">User Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Users</h5>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <x-datatable 
                    id="usersTable" 
                    :columns="['ID', 'Name', 'Email', 'Role', 'Status', 'Created', 'Actions']"
                    :exportButtons="true">
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-pill" style="background: #1a202c; color: #c1ec4a;">
                                    {{ $user->role->display_name }}
                                </span>
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </x-datatable>
            </div>
        </div>
    </div>
</div>
@endsection
```

## Tips & Best Practices

1. **Always use unique IDs** for each DataTable on a page
2. **Use card-wrapper** for better visual separation
3. **Keep column count reasonable** (max 7-8 columns)
4. **Use badges** for status indicators
5. **Include icons** for action buttons
6. **Set `p-0`** on card-body when using DataTable for seamless integration

## Technical Details

- **Library**: DataTables 1.13.7
- **Framework**: Bootstrap 4
- **JavaScript**: jQuery 3.3.1
- **Responsive**: Yes
- **Browser Support**: Modern browsers

## Files Modified

- `resources/views/layouts/app.blade.php` - Added DataTables CSS & JS
- `resources/views/components/datatable.blade.php` - Reusable component
- `resources/views/admin/users/index-datatable.blade.php` - Example usage

## Next Steps

1. Use the `<x-datatable>` component in any of your pages
2. Customize the columns and data as needed
3. Add export buttons if data export is required
4. Style badges and buttons to match your theme

---

**Theme Colors Reference**:
- Dark Teal: #1a202c
- Lime Green: #c1ec4a
- Hover Lime: #B0D93F
- Dark Teal Hover: #2D3748
