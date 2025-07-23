@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-xl mx-auto">
            <!-- Instagram-style notification layout -->
            <div class="bg-white rounded-lg shadow-sm border notification-layout">
                <!-- Header -->
                <div class="notification-header">
                    <h1 class="text-xl font-bold">{{ __('messages.notifications') }}</h1>
                    @if ($notifications->where('read_at', null)->count() > 0)
                        <button id="markAllRead"
                            class="text-blue-500 hover:text-blue-700 text-sm font-medium border border-gray-300 px-3 py-1 rounded">
                            {{ __('messages.mark_all_as_read') }}
                        </button>
                    @endif
                </div>

                <!-- Notifications list -->
                <div class="notification-list">
                    @forelse($notifications as $notification)
                        <div class="notification-item-instagram" data-notification-id="{{ $notification->id }}"
                            data-type="{{ $notification->type }}">

                            <!-- User Avatar -->
                            <div class="notification-avatar-instagram">
                                @if ($notification->sender)
                                    <a href="{{ route('profile.show', $notification->sender->id) }}"
                                        class="block w-full h-full">
                                        @if ($notification->sender->avatar)
                                            <img src="{{ $notification->sender->avatar }}"
                                                alt="{{ $notification->sender->name }}"
                                                class="w-full h-full rounded-full object-cover">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($notification->sender->name) }}&background=random"
                                                alt="{{ $notification->sender->name }}"
                                                class="w-full h-full rounded-full object-cover">
                                        @endif
                                    </a>
                                @else
                                    <div class="w-full h-full rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">
                                            {{ substr($notification->sender ? $notification->sender->name : 'U', 0, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Notification Content -->
                            <div class="notification-content-instagram">
                                <!-- Main notification text -->
                                <div class="notification-text-instagram">
                                    @if ($notification->sender)
                                        <a href="{{ route('profile.show', $notification->sender->id) }}"
                                            class="font-semibold hover:underline">
                                            {{ $notification->sender->name }}
                                        </a>
                                    @else
                                        <span class="font-semibold">{{ __('messages.someone') }}</span>
                                    @endif
                                    <span class="text-gray-600">
                                        {{ $notification->getMessage() }}
                                    </span>
                                </div>

                                <!-- Time -->
                                <div class="notification-time-instagram">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Right side: Follow button, heart icon, and unread indicator -->
                            <div class="notification-actions-instagram">
                                @if ($notification->type === 'follow')
                                    @if ($notification->sender && $notification->sender->isFollowed())
                                        <form action="{{ route('follow.destroy', $notification->sender->id) }}"
                                            method="post" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="follow-btn-instagram following">{{ __('messages.following') }}</button>
                                        </form>
                                    @else
                                        <form action="{{ route('follow.store', $notification->sender->id) }}"
                                            method="post" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="follow-btn-instagram">{{ __('messages.follow') }}</button>
                                        </form>
                                    @endif
                                @endif
                                @if ($notification->notifiable && in_array($notification->type, ['like', 'comment']))
                                    <div class="post-preview-right">
                                        <a href="{{ route('post.show', $notification->notifiable->id) }}"
                                            class="text-xs text-decoration-none text-muted hover:text-blue-600 transition-colors">
                                            {{ __('messages.your_post') }}
                                        </a>

                                        <!-- Heart icon and post image preview -->
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                                                @if ($notification->type === 'like')
                                                    <i class="fas fa-heart instagram-heart text-sm"></i>
                                                @else
                                                    <i class="fas fa-comment text-blue-500 text-sm"></i>
                                                @endif
                                            </div>

                                            <!-- Post image preview if available -->
                                            @if ($notification->notifiable && method_exists($notification->notifiable, 'image'))
                                                <a href="{{ route('post.show', $notification->notifiable->id) }}">
                                                    <img src="{{ $notification->notifiable->image ?? 'https://via.placeholder.com/32x32' }}"
                                                        alt="Post preview"
                                                        class="w-8 h-8 rounded object-cover hover:opacity-80 transition-opacity">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if ($notification->isUnread())
                                    <span class="w-2 h-2 bg-blue-500 rounded-full unread-indicator"></span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <!-- Instagram-style empty state -->
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-heart text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('messages.all_caught_up') }}</h3>
                            <p class="text-gray-500 text-sm">{{ __('messages.all_caught_up_message') }}
                            </p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
