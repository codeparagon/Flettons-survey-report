@extends('layouts.app')

@section('title', 'Survey Section Builder')

@push('styles')
<style>
    /* ============================================
       SURVEY BUILDER - Clean Modern Design
       ============================================ */
    
    /* Page Header */
    .page-header-builder {
        background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        color: white;
        padding: 28px 32px;
        border-radius: 16px;
        margin-bottom: 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 40px rgba(26, 32, 44, 0.15);
    }
    
    .page-header-builder h1 {
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
        color: white!important;
    }
    
    .page-header-builder h1 i {
        color: #c1ec4a;
        font-size: 24px;
    }
    
    .header-actions {
        display: flex;
        gap: 12px;
    }
    
    .btn-header {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        text-decoration: none;
    }
    
    .btn-header-primary {
        background: #c1ec4a;
        color: #1a202c;
    }
    
    .btn-header-primary:hover {
        background: #a8d83a;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(193, 236, 74, 0.4);
    }
    
    .btn-header-secondary {
        background: rgba(255,255,255,0.1);
        color: white;
        border-color: rgba(255,255,255,0.2);
    }
    
    .btn-header-secondary:hover {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    /* Stats Bar */
    .stats-bar {
        display: flex;
        gap: 24px;
        background: white;
        padding: 20px 28px;
        border-radius: 14px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .stat-icon.categories, .stat-icon.subcategories , .stat-icon.sections {
        background: #eef2ff;
        color: #374151;
    }
    
    .stat-info h4 {
        font-size: 22px;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
        line-height: 1;
    }
    
    .stat-info span {
        font-size: 13px;
        color: #6b7280;
    }
    
    /* Main Content Layout */
    .builder-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        align-items: start;
    }
    
    @media (max-width: 1200px) {
        .builder-layout {
            grid-template-columns: 1fr;
        }
        .preview-panel {
            display: none;
        }
    }
    
    /* Tree Panel */
    .tree-panel {
        background: white;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .tree-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .bulk-select {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .bulk-select label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #6b7280;
        cursor: pointer;
        margin: 0;
    }
    
    /* Custom Themed Checkboxes */
    .custom-check {
        position: relative;
        width: 20px;
        height: 20px;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background: white;
        border: 2px solid #d1d5db;
        border-radius: 5px;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    
    .custom-check:hover {
        border-color: #1a202c;
    }
    
    .custom-check:checked {
        background: #1a202c;
        border-color: #1a202c;
    }
    
    .custom-check:checked::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 6px;
        width: 5px;
        height: 10px;
        border: solid #c1ec4a;
        border-width: 0 2.5px 2.5px 0;
        transform: rotate(45deg);
    }
    
    .custom-check:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.3);
    }
    
    .bulk-btns {
        display: none;
        gap: 8px;
    }
    
    .bulk-btns.active {
        display: flex;
    }
    
    .bulk-btns button {
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    
    .bulk-btns .btn-enable {
        background: #dcfce7;
        color: #166534;
    }
    
    .bulk-btns .btn-disable {
        background: #fef3c7;
        color: #92400e;
    }
    
    .bulk-btns .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .tree-content {
        padding: 20px;
        max-height: calc(100vh - 380px);
        overflow-y: auto;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 30px;
    }
    
    .empty-state i {
        font-size: 56px;
        color: #d1d5db;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        font-size: 20px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        color: #6b7280;
        font-size: 15px;
        margin-bottom: 24px;
    }
    
    /* Category Item */
    .cat-item {
        background: #fafafa;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 16px;
        overflow: hidden;
        transition: all 0.2s;
    }
    
    .cat-item:hover {
        border-color: #c1ec4a;
    }
    
    .cat-item.collapsed .cat-body {
        display: none;
    }
    
    .cat-head {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        cursor: pointer;
        transition: background 0.2s;
        gap: 12px;
    }
    
    .cat-head:hover {
        background: #f5f5f5;
    }
    
    .drag-handle {
        color: #9ca3af;
        cursor: grab;
        padding: 4px;
    }
    
    .drag-handle:active {
        cursor: grabbing;
    }
    
    /* Category checkbox inherits custom-check styles */
    
    .cat-toggle {
        color: #6b7280;
        transition: transform 0.2s;
        font-size: 14px;
    }
    
    .cat-item.collapsed .cat-toggle {
        transform: rotate(-90deg);
    }
    
    .cat-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #1a202c, #374151);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c1ec4a;
        font-size: 16px;
    }
    
    .cat-info {
        flex: 1;
    }
    
    .cat-name {
        font-weight: 600;
        font-size: 16px;
        color: #1a202c;
        margin-bottom: 2px;
    }
    
    .cat-name[contenteditable="true"] {
        outline: none;
        background: #c1ec4a;
        padding: 2px 8px;
        border-radius: 4px;
    }
    
    .cat-meta {
        font-size: 13px;
        color: #6b7280;
    }
    
    .cat-actions {
        display: flex;
        gap: 6px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .cat-head:hover .cat-actions {
        opacity: 1;
    }
    
    .act-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        background: white;
        color: #6b7280;
        font-size: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    
    .act-btn:hover {
        background: #1a202c;
        color: #c1ec4a;
        transform: translateY(-1px);
    }
    
    .act-btn.danger:hover {
        background: #ef4444;
        color: white;
    }
    
    .cat-body {
        padding: 0 20px 20px 20px;
    }
    
    /* Subcategory Item */
    .subcat-list {
        margin-left: 50px;
        margin-top: 8px;
    }
    
    .subcat-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 10px;
        overflow: hidden;
    }
    
    .subcat-item.collapsed .subcat-body {
        display: none;
    }
    
    .subcat-head {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        cursor: pointer;
        gap: 10px;
    }
    
    .subcat-head:hover {
        background: #f9fafb;
    }
    
    .subcat-toggle {
        color: #9ca3af;
        font-size: 12px;
        transition: transform 0.2s;
    }
    
    .subcat-item.collapsed .subcat-toggle {
        transform: rotate(-90deg);
    }
    
    .subcat-name {
        flex: 1;
        font-weight: 500;
        font-size: 14px;
        color: #374151;
    }
    
    .subcat-name[contenteditable="true"] {
        outline: none;
        background: #fef3c7;
        padding: 2px 8px;
        border-radius: 4px;
    }
    
    .subcat-count {
        font-size: 12px;
        color: #9ca3af;
        margin-right: 8px;
    }
    
    .subcat-actions {
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .subcat-head:hover .subcat-actions {
        opacity: 1;
    }
    
    .act-btn-sm {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 12px;
    }
    
    .act-btn-sm:hover {
        background: #1a202c;
        color: #c1ec4a;
    }
    
    .act-btn-sm.danger:hover {
        background: #ef4444;
        color: white;
    }
    
    /* Clone action button - distinct purple/indigo style */
    .act-btn-sm.clone-action {
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
    }
    
    .act-btn-sm.clone-action:hover {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
        transform: scale(1.05);
    }
    
    .subcat-body {
        padding: 0 16px 12px 16px;
    }
    
    /* Section Item */
    .sec-list {
        margin-left: 38px;
        margin-top: 6px;
    }
    
    .sec-item {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 6px;
        gap: 10px;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .sec-item:hover {
        background: white;
        border-color: #c1ec4a;
        box-shadow: 0 2px 8px rgba(193, 236, 74, 0.2);
    }
    
    .sec-item.selected {
        background: #f0fdf4;
        border-color: #22c55e;
    }
    
    .sec-item.inactive {
        opacity: 0.5;
    }
    
    .sec-name {
        flex: 1;
        font-size: 14px;
        color: #374151;
        font-weight: 500;
    }
    
    .sec-name[contenteditable="true"] {
        outline: none;
        background: white;
        padding: 2px 8px;
        border-radius: 4px;
        border: 1px solid #c1ec4a;
    }
    
    .sec-badges {
        display: flex;
        gap: 4px;
        margin-right: 8px;
    }
    
    .badge-sm {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-level {
        background: #1a202c;
        color: #c1ec4a;
    }
    
    /* Clonable indicator - subtle info badge */
    .badge-clonable {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        color: #166534;
        border: 1px solid #86efac;
        font-size: 9px;
        padding: 2px 6px;
        gap: 3px;
        display: inline-flex;
        align-items: center;
    }
    
    .badge-clonable i {
        font-size: 8px;
    }
    
    .badge-inactive {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .sec-actions {
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .sec-item:hover .sec-actions {
        opacity: 1;
    }
    
    /* Add Button */
    .add-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        background: transparent;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        font-size: 14px;
        font-weight: 500;
        margin-top: 12px;
    }
    
    .add-btn:hover {
        border-color: #c1ec4a;
        background: #fefce8;
        color: #1a202c;
    }
    
    /* Preview Panel */
    .preview-panel {
        background: white;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 200px);
        overflow: hidden;
    }
    
    .preview-head {
        background: linear-gradient(135deg, #1a202c, #374151);
        color: white;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .preview-head h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }
    
    .preview-head i {
        color: #c1ec4a;
    }
    
    .preview-body {
        padding: 20px;
        max-height: calc(100vh - 300px);
        overflow-y: auto;
    }
    
    .preview-empty {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }
    
    .preview-empty i {
        font-size: 40px;
        margin-bottom: 14px;
        display: block;
    }
    
    .preview-section {
        background: #f9fafb;
        border-radius: 10px;
        padding: 18px;
    }
    
    .preview-section h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #c1ec4a;
    }
    
    .preview-field {
        margin-bottom: 16px;
    }
    
    .preview-label {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    
    .preview-options {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .preview-opt {
        padding: 8px 12px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        color: #374151;
    }
    
    /* Modals */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal-box {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease;
    }
    
    @keyframes modalSlideIn {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        background: linear-gradient(135deg, #1a202c, #374151);
        color: white;
    }
    
    .modal-head h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }
    
    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 4px;
        opacity: 0.7;
        line-height: 1;
    }
    
    .modal-close:hover {
        opacity: 1;
    }
    
    .modal-body {
        padding: 24px;
        max-height: calc(90vh - 180px);
        overflow-y: auto;
    }
    
    .modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    
    /* Form Elements */
    .form-grp {
        margin-bottom: 20px;
    }
    
    .form-lbl {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-lbl .required {
        color: #ef4444;
    }
    
    .form-ctrl {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }
    
    .form-ctrl:focus {
        outline: none;
        border-color: #c1ec4a;
        box-shadow: 0 0 0 4px rgba(193, 236, 74, 0.2);
    }
    
    .form-ctrl[readonly] {
        background: #f9fafb;
        color: #6b7280;
    }
    
    .form-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }
    
    /* Checkbox Group for Level Selection */
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        font-weight: 500;
        background: white;
        position: relative;
    }
    
    .checkbox-item::before {
        content: '';
        width: 18px;
        height: 18px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    
    .checkbox-item:hover {
        border-color: #1a202c;
        background: #f9fafb;
    }
    
    .checkbox-item:hover::before {
        border-color: #1a202c;
    }
    
    .checkbox-item.selected {
        background: #1a202c;
        border-color: #1a202c;
        color: #c1ec4a;
        font-weight: 600;
    }
    
    .checkbox-item.selected::before {
        background: #c1ec4a;
        border-color: #c1ec4a;
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%231a202c' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        background-size: 14px;
        background-position: center;
        background-repeat: no-repeat;
    }
    
    .checkbox-item input {
        display: none;
    }
    
    /* Clonable option checkbox */
    .clonable-option {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        background: white;
    }
    
    .clonable-option::before {
        content: '';
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 5px;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    
    .clonable-option:hover {
        border-color: #1a202c;
    }
    
    .clonable-option.selected {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border-color: #22c55e;
        color: #166534;
    }
    
    .clonable-option.selected::before {
        background: #22c55e;
        border-color: #22c55e;
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        background-size: 14px;
        background-position: center;
        background-repeat: no-repeat;
    }
    
    .clonable-option input {
        display: none;
    }
    
    
    
    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    
    .btn-primary {
        background: #1a202c;
        color: #c1ec4a;
    }
    
    .btn-primary:hover {
        background: #374151;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border-color: #e5e7eb;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    
    .btn-success {
        background: #c1ec4a;
        color: #1a202c;
    }
    
    .btn-success:hover {
        background: #a8d83a;
        transform: translateY(-1px);
    }
    
    /* Toast */
    .toast-wrap {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
    }
    
    .toast-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        background: #1a202c;
        color: white;
        border-radius: 10px;
        margin-bottom: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        animation: toastIn 0.3s ease;
        font-size: 14px;
    }
    
    .toast-item.success {
        background: #16a34a;
    }
    
    .toast-item.error {
        background: #dc2626;
    }
    
    @keyframes toastIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Sortable Effects */
    .sortable-ghost {
        opacity: 0.4;
        background: #fef3c7 !important;
    }
    
    .sortable-chosen {
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header-builder">
    <h1>
        <i class="fas fa-sitemap"></i>
        Survey Section Builder
    </h1>
    <div class="header-actions">
        <a href="{{ route('admin.survey-options.index') }}" class="btn-header btn-header-secondary">
            <i class="fas fa-cog"></i> Global Options
        </a>
        <button class="btn-header btn-header-primary" onclick="openAddCategoryModal()">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>
</div>

<!-- Stats Bar -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-icon categories">
            <i class="fas fa-folder"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $categories->count() }}</h4>
            <span>Categories</span>
        </div>
    </div>
    <div class="stat-item">
        <div class="stat-icon subcategories">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $categories->sum(fn($c) => $c->subcategories->count()) }}</h4>
            <span>Subcategories</span>
        </div>
    </div>
    <div class="stat-item">
        <div class="stat-icon sections">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-info">
            <h4>{{ $categories->sum(fn($c) => $c->subcategories->sum(fn($s) => $s->sectionDefinitions->count())) }}</h4>
            <span>Sections</span>
        </div>
    </div>
</div>

<!-- Main Layout -->
<div class="builder-layout">
    <!-- Tree Panel -->
    <div class="tree-panel">
        <div class="tree-toolbar">
            <div class="bulk-select">
                <label>
                    <input type="checkbox" class="custom-check" id="selectAllBox" onchange="toggleSelectAll()">
                    Select All
                </label>
                <div class="bulk-btns" id="bulkBtns">
                    <button class="btn-enable" onclick="bulkEnable()"><i class="fas fa-check"></i> Enable</button>
                    <button class="btn-disable" onclick="bulkDisable()"><i class="fas fa-ban"></i> Disable</button>
                    <button class="btn-delete" onclick="bulkDelete()"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </div>
        </div>
        
        <div class="tree-content" id="categoryList">
            @forelse($categories as $category)
                @include('admin.survey-builder.partials.category-item', ['category' => $category])
            @empty
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Categories Yet</h3>
                    <p>Start building your survey structure by adding a category.</p>
                    <button class="btn btn-success" onclick="openAddCategoryModal()">
                        <i class="fas fa-plus"></i> Add First Category
                    </button>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Preview Panel -->
    <div class="preview-panel" id="previewPanel">
        <div class="preview-head">
            <i class="fas fa-eye"></i>
            <h3>Live Preview</h3>
        </div>
        <div class="preview-body" id="previewContent">
            <div class="preview-empty">
                <i class="fas fa-hand-pointer"></i>
                <p>Click on a section to preview how it will appear to surveyors.</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal-overlay" id="addCategoryModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Add Category</h3>
            <button class="modal-close" onclick="closeModal('addCategoryModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addCategoryForm">
                <div class="form-grp">
                    <label class="form-lbl">Category Name <span class="required">*</span></label>
                    <input type="text" class="form-ctrl" name="display_name" required placeholder="e.g., Exterior">
                </div>
                <div class="form-grp">
                    <label class="form-lbl">System Name</label>
                    <input type="text" class="form-ctrl" name="name" readonly placeholder="Auto-generated">
                    <p class="form-hint">System name is automatically generated from the category name.</p>
                </div>
                <div class="form-grp">
                    <label class="form-lbl">Icon</label>
                    <input type="text" class="form-ctrl" name="icon" placeholder="e.g., fas fa-home">
                    <p class="form-hint">Use FontAwesome class names (e.g., fas fa-home, fas fa-building)</p>
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="closeModal('addCategoryModal')">Cancel</button>
            <button class="btn btn-success" onclick="saveCategory()">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>
    </div>
</div>

<!-- Add Subcategory Modal -->
<div class="modal-overlay" id="addSubcategoryModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Add Subcategory</h3>
            <button class="modal-close" onclick="closeModal('addSubcategoryModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addSubcategoryForm">
                <input type="hidden" name="category_id" id="subcatCategoryId">
                <div class="form-grp">
                    <label class="form-lbl">Parent Category</label>
                    <input type="text" class="form-ctrl" id="subcatCategoryName" readonly>
                </div>
                <div class="form-grp">
                    <label class="form-lbl">Subcategory Name <span class="required">*</span></label>
                    <input type="text" class="form-ctrl" name="display_name" required placeholder="e.g., Roofing">
                </div>
                <div class="form-grp">
                    <label class="form-lbl">System Name</label>
                    <input type="text" class="form-ctrl" name="name" readonly placeholder="Auto-generated">
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="closeModal('addSubcategoryModal')">Cancel</button>
            <button class="btn btn-success" onclick="saveSubcategory()">
                <i class="fas fa-plus"></i> Add Subcategory
            </button>
        </div>
    </div>
</div>

<!-- Add/Edit Section Modal - Simple Form -->
<div class="modal-overlay" id="sectionModal">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-head">
            <h3 id="sectionModalTitle">Add Section</h3>
            <button class="modal-close" onclick="closeModal('sectionModal')">&times;</button>
        </div>
        
        <div class="modal-body">
            <form id="sectionForm">
                <input type="hidden" name="subcategory_id" id="secSubcatId">
                <input type="hidden" name="section_id" id="secEditId">
                
                <div class="form-grp">
                    <label class="form-lbl">Section Name <span class="required">*</span></label>
                    <input type="text" class="form-ctrl" name="display_name" required placeholder="e.g., Main Roof, Chimneys, External Walls">
                </div>
                
                <div class="form-grp">
                    <label class="form-lbl">System Name</label>
                    <input type="text" class="form-ctrl" name="name" readonly placeholder="Auto-generated from name">
                </div>
                
                @if($levels->count() > 0)
                <div class="form-grp">
                    <label class="form-lbl">Assign to Survey Levels</label>
                    <div class="checkbox-group" id="levelBoxes">
                        @foreach($levels as $level)
                        <label class="checkbox-item" onclick="toggleCheckbox(this)">
                            <input type="checkbox" name="levels[]" value="{{ $level->id }}">
                            {{ $level->display_name }}
                        </label>
                        @endforeach
                    </div>
                    <p class="form-hint">Select which survey levels should include this section.</p>
                </div>
                @endif
                
                <div class="form-grp" style="margin-bottom: 0;">
                    <label class="clonable-option" onclick="toggleCheckbox(this)">
                        <input type="checkbox" name="is_clonable" value="1">
                        <i class="fas fa-copy"></i> Allow duplication
                    </label>
                    <p class="form-hint" style="margin-top: 8px;">Surveyors can create multiple copies (e.g., for multiple roof areas).</p>
                </div>
            </form>
        </div>
        
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="closeModal('sectionModal')">Cancel</button>
            <button class="btn btn-success" onclick="saveSection()">
                <i class="fas fa-check"></i> <span id="sectionSaveText">Add Section</span>
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-wrap" id="toastWrap"></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function() {
    initSortables();
    initAutoSlug();
});

// Sortable
function initSortables() {
    const catList = document.getElementById('categoryList');
    if (catList && catList.querySelector('.cat-item')) {
        new Sortable(catList, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: () => reorderCategories()
        });
    }
    
    document.querySelectorAll('.subcat-list').forEach(list => {
        new Sortable(list, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: () => reorderSubcategories(list)
        });
    });
    
    document.querySelectorAll('.sec-list').forEach(list => {
        new Sortable(list, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: () => reorderSections(list)
        });
    });
}

// Auto slug
function initAutoSlug() {
    document.querySelectorAll('input[name="display_name"]').forEach(input => {
        input.addEventListener('input', function() {
            const form = this.closest('form');
            const nameInput = form.querySelector('input[name="name"]');
            if (nameInput && nameInput.hasAttribute('readonly')) {
                nameInput.value = slugify(this.value);
            }
        });
    });
}

function slugify(text) {
    return text.toLowerCase().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '_').replace(/^-+|-+$/g, '');
}

// Toggles
function toggleCat(el) {
    el.closest('.cat-item').classList.toggle('collapsed');
}

function toggleSubcat(el) {
    el.closest('.subcat-item').classList.toggle('collapsed');
}

function toggleCheckbox(label) {
    const input = label.querySelector('input');
    input.checked = !input.checked;
    label.classList.toggle('selected', input.checked);
}

// Modals
function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    const form = document.querySelector('#' + id + ' form');
    if (form) form.reset();
    // Reset checkbox visual states
    document.querySelectorAll('.checkbox-item, .clonable-option').forEach(item => item.classList.remove('selected'));
}

function openAddCategoryModal() {
    openModal('addCategoryModal');
}

function openAddSubcatModal(catId, catName) {
    document.getElementById('subcatCategoryId').value = catId;
    document.getElementById('subcatCategoryName').value = catName;
    openModal('addSubcategoryModal');
}

function openAddSecModal(subcatId) {
    document.getElementById('secSubcatId').value = subcatId;
    document.getElementById('secEditId').value = '';
    document.getElementById('sectionModalTitle').textContent = 'Add Section';
    document.getElementById('sectionSaveText').textContent = 'Add Section';
    openModal('sectionModal');
}

function openEditSecModal(secId) {
    document.getElementById('secEditId').value = secId;
    document.getElementById('sectionModalTitle').textContent = 'Edit Section';
    document.getElementById('sectionSaveText').textContent = 'Update Section';
    
    fetch(`/admin/api/section-definitions/${secId}`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const sec = data.section;
            document.getElementById('secSubcatId').value = sec.subcategory_id;
            document.querySelector('#sectionForm input[name="display_name"]').value = sec.display_name;
            document.querySelector('#sectionForm input[name="name"]').value = sec.name;
            
            // Set clonable checkbox
            const clonableOpt = document.querySelector('.clonable-option');
            const clonableInput = clonableOpt.querySelector('input');
            if (sec.is_clonable) {
                clonableInput.checked = true;
                clonableOpt.classList.add('selected');
            }
            
            // Set level checkboxes (if levels data is returned)
            if (data.levels) {
                data.levels.forEach(levelId => {
                    const checkbox = document.querySelector(`#levelBoxes input[value="${levelId}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.checkbox-item').classList.add('selected');
                    }
                });
            }
        }
        openModal('sectionModal');
    });
}

// API
async function api(url, method = 'GET', data = null) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
    };
    if (data && method !== 'GET') opts.body = JSON.stringify(data);
    const res = await fetch(url, opts);
    return res.json();
}

// Save functions
async function saveCategory() {
    const form = document.getElementById('addCategoryForm');
    const data = Object.fromEntries(new FormData(form));
    data.name = data.display_name;
    
    try {
        const result = await api('/admin/api/categories', 'POST', data);
        if (result.success) {
            toast('Category created!', 'success');
            closeModal('addCategoryModal');
            location.reload();
        } else {
            toast(result.message || 'Error', 'error');
        }
    } catch (e) {
        toast('Error creating category', 'error');
    }
}

async function saveSubcategory() {
    const form = document.getElementById('addSubcategoryForm');
    const data = Object.fromEntries(new FormData(form));
    data.name = data.display_name;
    
    try {
        const result = await api('/admin/api/subcategories', 'POST', data);
        if (result.success) {
            toast('Subcategory created!', 'success');
            closeModal('addSubcategoryModal');
            location.reload();
        } else {
            toast(result.message || 'Error', 'error');
        }
    } catch (e) {
        toast('Error creating subcategory', 'error');
    }
}

async function saveSection() {
    const form = document.getElementById('sectionForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Auto-generate system name
    data.name = slugify(data.display_name);
    
    // Get selected levels
    data.levels = Array.from(form.querySelectorAll('input[name="levels[]"]:checked')).map(c => c.value);
    
    // Get clonable status
    data.is_clonable = form.querySelector('input[name="is_clonable"]').checked ? 1 : 0;
    
    const isEdit = data.section_id && data.section_id !== '';
    const url = isEdit ? `/admin/api/section-definitions/${data.section_id}` : '/admin/api/section-definitions';
    const method = isEdit ? 'PUT' : 'POST';
    
    try {
        const result = await api(url, method, data);
        if (result.success) {
            toast(`Section ${isEdit ? 'updated' : 'created'}!`, 'success');
            closeModal('sectionModal');
            location.reload();
        } else {
            toast(result.message || 'Error', 'error');
        }
    } catch (e) {
        toast('Error saving section', 'error');
    }
}

// Delete functions
async function deleteCat(id) {
    if (!confirm('Delete this category and all its contents?')) return;
    try {
        const result = await api(`/admin/api/categories/${id}`, 'DELETE');
        if (result.success) {
            toast('Category deleted', 'success');
            document.querySelector(`.cat-item[data-id="${id}"]`).remove();
        } else {
            toast(result.message || 'Error', 'error');
        }
    } catch (e) {
        toast('Error deleting', 'error');
    }
}

async function deleteSubcat(id) {
    if (!confirm('Delete this subcategory?')) return;
    try {
        const result = await api(`/admin/api/subcategories/${id}`, 'DELETE');
        if (result.success) {
            toast('Subcategory deleted', 'success');
            document.querySelector(`.subcat-item[data-id="${id}"]`).remove();
        } else {
            toast(result.message || 'Error', 'error');
        }
    } catch (e) {
        toast('Error deleting', 'error');
    }
}

async function deleteSec(id) {
    if (!confirm('Delete this section?')) return;
    try {
        const result = await api(`/admin/api/section-definitions/${id}`, 'DELETE');
        if (result.success) {
            toast('Section deleted', 'success');
            document.querySelector(`.sec-item[data-id="${id}"]`).remove();
        } else {
            toast(result.message || 'Error', 'error');
        }
    } catch (e) {
        toast('Error deleting', 'error');
    }
}

async function cloneSec(id) {
    try {
        const result = await api(`/admin/api/section-definitions/${id}/clone`, 'POST');
        if (result.success) {
            toast('Section cloned!', 'success');
            location.reload();
        } else {
            toast(result.message || 'Error', 'error');
        }
    } catch (e) {
        toast('Error cloning', 'error');
    }
}

// Reorder
async function reorderCategories() {
    const order = Array.from(document.querySelectorAll('.cat-item')).map(i => i.dataset.id);
    await api('/admin/api/categories/reorder', 'POST', { order });
}

async function reorderSubcategories(list) {
    const order = Array.from(list.querySelectorAll('.subcat-item')).map(i => i.dataset.id);
    await api('/admin/api/subcategories/reorder', 'POST', { order });
}

async function reorderSections(list) {
    const order = Array.from(list.querySelectorAll('.sec-item')).map(i => i.dataset.id);
    await api('/admin/api/section-definitions/reorder', 'POST', { order });
}

// Inline edit
function enableEdit(el) {
    el.contentEditable = true;
    el.focus();
    document.execCommand('selectAll', false, null);
    el.addEventListener('blur', saveEdit, { once: true });
    el.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); el.blur(); }
        if (e.key === 'Escape') { el.contentEditable = false; location.reload(); }
    });
}

async function saveEdit(e) {
    const el = e.target;
    el.contentEditable = false;
    const type = el.dataset.type;
    const id = el.dataset.id;
    const value = el.textContent.trim();
    
    try {
        await api(`/admin/api/${type}s/${id}`, 'PUT', { display_name: value });
        toast('Updated!', 'success');
    } catch (e) {
        toast('Error', 'error');
        location.reload();
    }
}

// Bulk actions
function toggleSelectAll() {
    const checked = document.getElementById('selectAllBox').checked;
    document.querySelectorAll('.item-check').forEach(c => c.checked = checked);
    updateBulkBtns();
}

function updateBulkBtns() {
    const count = document.querySelectorAll('.item-check:checked').length;
    document.getElementById('bulkBtns').classList.toggle('active', count > 0);
}

async function bulkEnable() { await doBulk('enable'); }
async function bulkDisable() { await doBulk('disable'); }
async function bulkDelete() {
    if (!confirm('Delete selected items?')) return;
    await doBulk('delete');
}

async function doBulk(action) {
    const items = { category: [], subcategory: [], section: [] };
    document.querySelectorAll('.item-check:checked').forEach(c => {
        items[c.dataset.type].push(c.dataset.id);
    });
    
    for (const type of ['section', 'subcategory', 'category']) {
        if (items[type].length > 0) {
            await api('/admin/api/bulk-action', 'POST', { action, type, ids: items[type] });
        }
    }
    
    toast(`Bulk ${action} done`, 'success');
    location.reload();
}

// Preview
function selectSec(id) {
    document.querySelectorAll('.sec-item').forEach(i => i.classList.remove('selected'));
    document.querySelector(`.sec-item[data-id="${id}"]`).classList.add('selected');
    loadPreview(id);
}

async function loadPreview(secId) {
    try {
        const result = await api(`/admin/api/preview?section_id=${secId}`);
        document.getElementById('previewContent').innerHTML = result.html;
    } catch (e) {
        console.error('Preview error');
    }
}

// Toast
function toast(msg, type = 'info') {
    const wrap = document.getElementById('toastWrap');
    const t = document.createElement('div');
    t.className = `toast-item ${type}`;
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i> ${msg}`;
    wrap.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
}

// Init checkbox listeners
document.querySelectorAll('.item-check').forEach(c => c.addEventListener('change', updateBulkBtns));
</script>
@endpush
