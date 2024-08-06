<x-admin::layouts>
    <x-slot:title>
    {{ __('api::app.notification.title') }}
    </x-slot>

    <div class="flex gap-4 justify-between items-center max-sm:flex-wrap">
        <p class="text-xl text-gray-800 dark:text-white font-bold">
            {{ __('api::app.notification.title') }}
        </p>

        <div class="flex gap-x-2.5 items-center">
            {!! view_render_event('bagisto.admin.settings.push-notification.index.create-button.before') !!}

            <a href="{{ route('api.notification.create') }}">
                <div class="primary-button">
                    {{ __('api::app.notification.add-title') }}
                </div>
            </a>

            {!! view_render_event('bagisto.admin.settings.push-notification.index.create-button.after') !!}
        </div>
    </div>

    {!! view_render_event('bagisto.admin.settings.push-notification.list.before') !!}

    <x-admin::datagrid src="{{ route('api.notification.index') }}" />

    {!! view_render_event('bagisto.admin.settings.push-notification.list.after') !!}

</x-admin::layouts>
