@extends('layouts.app')

@section('title', 'Dashboard - LegalHR Tanzania')

@section('content')
@if($dashboardType === 'employee')
    @include('dashboard.employee')
@elseif($dashboardType === 'manager')
    @include('dashboard.manager')
@else
    @include('dashboard.management')
@endif

@push('scripts')
<script>
// Fallback toggleNotifications function
if (typeof toggleNotifications === 'undefined') {
    function toggleNotifications() {
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) {
            notificationDropdown.classList.toggle('hidden');
        }
    }
}

// Fallback removeNotification function
if (typeof removeNotification === 'undefined') {
    function removeNotification(id) {
        const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
        if (notification) {
            notification.remove();
            updateNotificationBadge();
            showNotification('Notification removed', 'info', 2000);
        }
    }
}

// Fallback markAllAsRead function
if (typeof markAllAsRead === 'undefined') {
    function markAllAsRead() {
        const notifications = document.querySelectorAll('.notification-item');
        notifications.forEach(notification => {
            notification.classList.add('opacity-50');
        });

        // Update badge
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            badge.textContent = '0';
            badge.classList.add('hidden');
        }

        showNotification('All notifications marked as read', 'success');
    }
}

// Fallback updateNotificationBadge function
if (typeof updateNotificationBadge === 'undefined') {
    function updateNotificationBadge() {
        const badge = document.getElementById('notificationBadge');
        const notifications = document.querySelectorAll('.notification-item');
        if (badge && notifications) {
            const count = notifications.length;
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }
}
</script>
@endpush
@endsection