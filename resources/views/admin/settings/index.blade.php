@extends('admin.layout.dashboard')
@section("content")

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Setting</h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route("admin.dashboard")}}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">Setting</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Filter menu-->

                <!--end::Filter menu-->
                <!--begin::Secondary button-->
                <!--end::Secondary button-->
                <!--begin::Primary button-->
                <!--end::Primary button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    @include("admin.partials._messages")
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Contacts App- Add New Contact-->
            <div class="row g-7">

                <!--begin::Content-->
                <div class="col-xl-12">
                    <!--begin::Contacts-->
                    <div class="card card-flush h-lg-100" id="kt_contacts_main">
                        <!--begin::Card header-->
                        <div class="card-header pt-7" id="kt_chat_contacts_header">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Svg Icon | path: icons/duotune/communication/com005.svg-->
                                <span class="svg-icon svg-icon-1 me-2">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M11.8 5.2L17.7 8.6V15.4L11.8 18.8L5.90001 15.4V8.6L11.8 5.2ZM11.8 2C11.5 2 11.2 2.1 11 2.2L3.8 6.4C3.3 6.7 3 7.3 3 7.9V16.2C3 16.8 3.3 17.4 3.8 17.7L11 21.9C11.3 22 11.5 22.1 11.8 22.1C12.1 22.1 12.4 22 12.6 21.9L19.8 17.7C20.3 17.4 20.6 16.8 20.6 16.2V7.9C20.6 7.3 20.3 6.7 19.8 6.4L12.6 2.2C12.4 2.1 12.1 2 11.8 2Z" fill="currentColor"></path>
                                        <path d="M11.8 8.69995L8.90001 10.3V13.7L11.8 15.3L14.7 13.7V10.3L11.8 8.69995Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <h2>Setting</h2>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-5">
                            <!--begin::Form-->
                            <form class="form" id="setting_form" method="POST" action="{{ route('setting.store') }}"
                                  enctype='multipart/form-data'>
                                @csrf
                                @foreach($all_columns as $column)

                                    @if($column['type']=="text")
                                        <div class="fv-row mb-7 row">
                                            <label class="col-3 fs-6 fw-semibold form-label mt-3">{{$column['label']}}</label>
                                            <div class="col-9">
                                                <input class="{{$column['class']}}" type="text"
                                                       name="{{$column['name']}}"
                                                       placeholder="{{$column['place_holder']}}"
                                                       value="{{ isset($settings[$column['name']]) ? $settings[$column['name']] : ''}}">
                                            </div>
                                        </div>
                                    @endif

                                    @if($column['type']=="hidden")
                                        <input type="hidden" name="{{$column['name']}}" value="{{ isset
	                        ($settings[$column['name']]) ? $settings[$column['name']]: ''}}">
                                    @endif

                                    @if($column['type']=="file")
                                        <div class="fv-row mb-7 row">
                                            <label class="col-3 fs-6 fw-semibold form-label mt-3">{{$column['label']}}</label>
                                            <?php
                                            if (isset($settings[$column['name']])) {
                                                $settings[$column['name']] = $settings[$column['name']];
                                            } else {
                                                $settings[$column['name']] = 'abc.png';
                                            }
                                            ?>
                                            <div class="col-9">
                                                <div class="custom-file">
                                                    <input type="file" name="{{$column['name']}}"
                                                           class="{{$column['class']}}" id="{{$column['id']}}">
                                                    <label class="custom-file-label" for="customFile">Choose
                                                        file</label>
                                                    @if(File::exists('uploads/'.$settings[$column['name']]))
                                                        <img src="{{asset('uploads/'.$settings[$column['name']])}}"
                                                             style="{{$column['style']}}"
                                                             alt="{{$column['name']}} is not found"/>
                                                    @else
                                                        <img src="{{asset('uploads/img.png')}}"
                                                             style="{{$column['style']}}"
                                                             alt="{{$column['name']}} is not found"/>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($column['type']=="textarea")
                                        <div class="fv-row mb-7 row">
                                            <label class="col-3 fs-6 fw-semibold form-label mt-3">{{$column['label']}}</label>
                                            <div class="col-9">
														<textarea name="{{$column['name']}}"
                                                                  class="{{$column['class']}}"
                                                                  rows="{{isset($column['rows'] )? $column['rows'] :'2'}}"
                                                                  placeholder="{{$column['place_holder']}}"
                                                                  id="{{$column['id']}}">{{ isset($settings[$column['name']]) ? $settings[$column['name']] : ''}}
														</textarea>
                                            </div>
                                        </div>
                                    @endif
                                    @if($column['type']=="checkbox")
                                        <div class="fv-row mb-7 row">
                                            <label class="col-3 fs-6 fw-semibold form-label mt-3 col-form-label">{{$column['label']}}</label>
                                            <div class="col-9">
															 <span
                                                                 class="switch switch-outline switch-icon switch-success">
																<input name="{{$column['name']}}"
                                                                       class="{{$column['class']}}"
                                                                       type="checkbox"
                                                                       id="{{$column['id']}}"
                                                                       value="{{$column['value']}}"
                                                                ><span></span>
                                                                 </label>
                              </span>

                                            </div>
                                        </div>
                                    @endif

                                @endforeach



                                <!--end::Input group-->
                                <!--begin::Separator-->
                                <div class="separator mb-6"></div>
                                <!--end::Separator-->
                                <!--begin::Action buttons-->
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <a href="{{route("admin.dashboard")}}" class="btn btn-light me-3">Back</a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Save</span>
                                        <span class="indicator-progress">Please wait...
                                                        <span
                                                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                    <!--end::Button-->
                                </div>
                                <!--end::Action buttons-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Contacts-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Contacts App- Add New Contact-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
    <!--end::Content wrapper-->

@endsection
@section("scripts")
@endsection
