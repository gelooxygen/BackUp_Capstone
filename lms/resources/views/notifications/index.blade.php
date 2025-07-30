@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Notifications</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Notifications</li>
                        </ul>
                    </div>
                    <div class="col-auto text-end float-end ms-auto">
                        <button type="button" class="btn btn-primary" id="markAllRead">
                            <i class="fas fa-check-double"></i> Mark All Read
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">All Notifications</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="#" class="btn btn-outline-primary me-2" id="exportNotifications">
                                            <i class="fas fa-download"></i> Export
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            @if(auth()->user()->notifications->count() == 0)
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-bell text-primary" style="font-size: 4rem; opacity: 0.6;"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-3">No Notifications Found</h4>
                                    <p class="text-muted mb-4">You don't have any notifications at this time.</p>
                                </div>
                            @else
                                <!-- Notifications List -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Title</th>
                                                <th>Content</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(auth()->user()->notifications as $notification)
                                                <tr class="{{ $notification->read_at ? '' : 'table-primary' }}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($notification->data['type'] ?? false)
                                                                <span class="badge bg-{{ $notification->data['priority'] == 'urgent' ? 'danger' : ($notification->data['priority'] == 'high' ? 'warning' : 'info') }} bg-opacity-10 me-2">
                                                                    <i class="fas fa-{{ $notification->data['type'] == 'announcement' ? 'bullhorn' : ($notification->data['type'] == 'message' ? 'envelope' : 'bell') }} me-1"></i>
                                                                    {{ ucfirst($notification->data['type'] ?? 'notification') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <a href="#" class="text-dark fw-bold notification-title" data-notification-id="{{ $notification->id }}">
                                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                                @if(!$notification->read_at)
                                                                    <i class="fas fa-circle text-primary ms-1" style="font-size: 0.5rem;"></i>
                                                                @endif
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">
                                                            {{ Str::limit($notification->data['content'] ?? $notification->data['message'] ?? 'No content available', 80) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <div class="text-dark fw-medium">{{ $notification->created_at->format('M d, Y') }}</div>
                                                            <small class="text-muted">{{ $notification->created_at->format('h:i A') }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($notification->read_at)
                                                            <span class="badge bg-success">Read</span>
                                                        @else
                                                            <span class="badge bg-warning">Unread</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="actions">
                                                            @if(!$notification->read_at)
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-success-light me-2" onclick="markAsRead('{{ $notification->id }}')">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                            @else
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-warning-light me-2" onclick="markAsUnread('{{ $notification->id }}')">
                                                                    <i class="fas fa-envelope"></i>
                                                                </a>
                                                            @endif
                                                            <a href="javascript:void(0);" class="btn btn-sm bg-danger-light" onclick="deleteNotification('{{ $notification->id }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ auth()->user()->notifications()->paginate(15)->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Detail Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="notificationModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function markAsRead(id) {
        fetch(`/notifications/${id}/mark-read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function markAsUnread(id) {
        fetch(`/notifications/${id}/mark-unread`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function deleteNotification(id) {
        if (confirm('Are you sure you want to delete this notification?')) {
            fetch(`/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }

    // Mark all as read
    document.getElementById('markAllRead').addEventListener('click', function() {
        if (confirm('Mark all notifications as read?')) {
            fetch('/notifications/mark-all-read', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });

    // Notification title click handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('notification-title')) {
            e.preventDefault();
            const notificationId = e.target.getAttribute('data-notification-id');
            showNotificationDetails(notificationId);
        }
    });

    function showNotificationDetails(id) {
        // This would typically load notification details via AJAX
        // For now, we'll show a simple message
        document.getElementById('notificationModalBody').innerHTML = `
            <div class="text-center">
                <i class="fas fa-bell text-primary mb-3" style="font-size: 3rem;"></i>
                <h5>Notification Details</h5>
                <p>Detailed notification information would be displayed here.</p>
                <p>Notification ID: ${id}</p>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('notificationModal')).show();
    }

    // Export functionality
    document.getElementById('exportNotifications').addEventListener('click', function() {
        alert('Export functionality will be implemented here');
    });
</script>
@endsection 