<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('admin::app.catalog.categories.create.title')
    </x-slot>

    {!! view_render_event('bagisto.admin.settings.notification.create.before') !!}

    <!-- Category Create Form -->
    <x-admin::form
        :action="route('api.notification.store')"
        enctype="multipart/form-data"
    >
        {!! view_render_event('bagisto.admin.settings.notification.create.create_form_controls.before') !!}

        <div class="flex gap-4 justify-between items-center max-sm:flex-wrap">
            <p class="text-xl text-gray-800 dark:text-white font-bold">
                @lang('api::app.notification.new-notification')
            </p>

            <div class="flex gap-x-2.5 items-center">
                <!-- Cancel Button -->
                <a
                    href="{{ route('api.notification.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:hover:bg-gray-800 dark:text-white"
                >
                    @lang('pwa::app.admin.push-notification.back-btn')
                </a>

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('api::app.notification.create-btn-title')
                </button>
            </div>
        </div>

        <!-- Full Pannel -->
        <div class="flex gap-2.5 mt-3.5 max-xl:flex-wrap">

            <!-- Left Section -->
            <div class="flex flex-col gap-2 flex-1 max-xl:flex-auto">

                {!! view_render_event('bagisto.admin.settings.notification.create.card.general.before') !!}

                <!-- General -->
                <div class="p-4 bg-white dark:bg-gray-900 rounded box-shadow">
                    <p class="mb-4 text-base text-gray-800 dark:text-white font-semibold">
                        @lang('pwa::app.admin.push-notification.general')
                    </p>

                    <!-- Title -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('api::app.notification.notification-title')
                        </x-admin::form.control-group.label>

                        <v-field
                            type="text"
                            name="title"
                            rules="required"
                            value="{{ old('title') }}"
                            v-slot="{ field }"
                            label="{{ trans('api::app.notification.notification-title') }}"
                        >
                            <input
                                type="text"
                                id="name"
                                :class="[errors['{{ 'name' }}'] ? 'border border-red-600 hover:border-red-600' : '']"
                                class="flex w-full min-h-[39px] py-2 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 transition-all hover:border-gray-400 dark:hover:border-gray-400 focus:border-gray-400 dark:focus:border-gray-400 dark:bg-gray-900 dark:border-gray-800"
                                name="name"
                                v-bind="field"
                                placeholder="{{ trans('pwa::app.admin.push-notification.label-title') }}"
                                v-slugify-target:slug="setValues"
                            >
                        </v-field>

                        <x-admin::form.control-group.error control-name="title" />
                    </x-admin::form.control-group>

                    <!-- Description -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('api::app.notification.notification-content')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            id="content"
                            class="description"
                            rules="required"
                            name="content"
                            :value="old('content')"
                            :label="trans('api::app.notification.notification-content')"
                            :tinymce="true"
                        />

                        <x-admin::form.control-group.error control-name="content" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('api::app.notification.store-view')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="select"
                            id="channels"
                            class="cursor-pointer"
                            name="channels[]"
                            multiple="multiple"
                            rules="required"
                            :value="old('channels')"
                            :label="trans('api::app.notification.store-view')"
                        >
                            @foreach ($channels as $channel)
                                <option value="{{ $channel->code }}">
                                    {{ $channel->name }}
                                </option>
                            @endforeach
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="channels" />
                    </x-admin::form.control-group>

                </div>


                {!! view_render_event('bagisto.admin.settings.notification.create.card.general.after') !!}
            </div>

            <!-- Right Section -->
            <div class="flex flex-col gap-2 w-[360px] max-w-full rounded box-shadow">
                <!-- Icon -->
                {!! view_render_event('bagisto.admin.settings.notification.create.card.log.before') !!}

                        <div class="p-2.5 flex flex-col gap-2 w-full">
                            <x-admin::form.control-group.label>
                                @lang('api::app.notification.notification-image')
                            </x-admin::form.control-group.label>
                            <x-admin::media.images name="image" />

                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label>
                                    @lang('api::app.notification.notification-status')
                                </x-admin::form.control-group.label>

                                @php $selectedValue = old('status') ?: 0 @endphp

                                <!-- Visible in menu Hidden field -->
                                <x-admin::form.control-group.control
                                    type="hidden"
                                    class="cursor-pointer"
                                    name="status"
                                    :checked="(boolean) $selectedValue"
                                />

                                <x-admin::form.control-group.control
                                    type="switch"
                                    class="cursor-pointer"
                                    name="status"
                                    value="1"
                                    :label="trans('admin::app.catalog.categories.edit.visible-in-menu')"
                                    :checked="(boolean) $selectedValue"
                                />
                            </x-admin::form.control-group>

                            <v-option-wrapper></v-option-wrapper>
                        </div>


                {!! view_render_event('bagisto.admin.settings.notification.create.card.logo.after') !!}
            </div>
        </div>

        {!! view_render_event('bagisto.admin.settings.notification.create.create_form_controls.after') !!}

    </x-admin::form>

    {!! view_render_event('bagisto.admin.settings.notification.create.after') !!}

    @pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-option-wrapper-template"
    >
        <div>
            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('api::app.notification.notification-type')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    id="type"
                    class="cursor-pointer"
                    name="type"
                    rules="required"
                    :value="old('type')"
                    :label="trans('api::app.notification.notification-type')"
                    v-model="notificationType"
                    @change="showHideOptions($event)"
                >
                     <option value="">
                        @lang('api::app.notification.notification-type-option.select')
                    </option>
                    <option value="others">
                        @lang('api::app.notification.notification-type-option.simple')
                    </option>
                    <option value="product">
                        @lang('api::app.notification.notification-type-option.product')
                    </option>
                    <option value="category">
                        @lang('api::app.notification.notification-type-option.category')
                    </option>
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="type" />
            </x-admin::form.control-group>

            <div
                class="control-group"
                v-if="showProductCategory"
                id="product_category"
            >
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('api::app.notification.product-cat-id')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        id="product_category_id"
                        class="cursor-pointer"
                        name="product_category_id"
                        rules="required"
                        :value="old('product_category_id')"
                        :label="trans('api::app.notification.product-cat-id')"
                        v-model="productCategoryInputBox"
                        @keyup="checkIdExistOrNot"
                    >

                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error control-name="product_category_id" />
                    <span class="mt-1 text-red-600 text-xs italic" v-if="message">
                        @{{message}}
                    </span>
                </x-admin::form.control-group>

            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-option-wrapper', {
            template: '#v-option-wrapper-template',

            data() {
                return {
                    showProductCategory: false,
                    valid: '',
                    notificationType : '',
                    productCategoryInputBox : '{{ old('product_category_id') }}',
                    message: '',
                    isValid: false,
                }
            },

            methods: {
                showHideOptions: function (event) {

                    this.notificationType = event.target.value;

                    this.showProductCategory = false;
                    if (event.target.value == 'product' || event.target.value == 'category' ) {
                        this.showProductCategory = true;
                    }
                },

                //id exist or not
                checkIdExistOrNot(event) {
                    var selectedType = this.notificationType;
                    var givenValue = this.productCategoryInputBox;
                    var spaceCount = (givenValue.split(" ").length - 1);
                    this.message = '';

                    if (spaceCount > 0) {
                        this.isValid = true;
                        return false;
                    }

                    this.$axios.post("{{ route('api.notification.cat-product-id') }}",{givenValue:givenValue, selectedType:selectedType})

                    .then(response => {
                        if(response.data.value) {
                            this.isValid = response.data.value;
                            this.message = response.data.message;
                        } else {
                            this.message = response.data.message;
                            this.isValid = response.data.value;
                        }
                    }).catch(function (error) {
                        console.log(error);;
                    });
                },
            },
        });

    </script>

    @endPushOnce

</x-admin::layouts>
