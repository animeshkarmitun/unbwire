@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.About Page') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.About Page') }}</h4>

            </div>

            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    @foreach ($languages as $language)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->index === 0 ? 'active' : '' }}" id="home-tab2" data-toggle="tab"
                                href="#home-{{ $language->lang }}" role="tab" aria-controls="home"
                                aria-selected="true">{{ $language->name }}</a>
                        </li>
                    @endforeach

                </ul>
                <div class="tab-content tab-bordered" id="myTab3Content">
                    @foreach ($languages as $language)
                        @php
                            $about = \App\Models\About::where('language', $language->lang)->first();
                        @endphp
                        <div class="tab-pane fade show {{ $loop->index === 0 ? 'active' : '' }}"
                            id="home-{{ $language->lang }}" role="tabpanel" aria-labelledby="home-tab2">
                            <div class="card-body">
                                <form action="{{ route('admin.about.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')


                                    <div class="form-group">
                                        <label for="">{{ __('admin.About Content') }}</label>
                                        <textarea name="content" class="summernote-{{ $language->lang }}" id="" cols="30" rows="10">{!! @$about->content !!}</textarea>
                                        <input type="hidden" name="language" value="{{ $language->lang }}">

                                    </div>

                                    <div class="form-group">
                                        <label for="">{{ __('admin.Meta Title') }}</label>
                                        <input type="text" class="form-control" name="meta_title" value="{{ @$about->meta_title }}" placeholder="{{ __('admin.Meta Title') }}">
                                        <small class="form-text text-muted">{{ __('admin.Max 255 characters') }}</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="">{{ __('admin.Meta Description') }}</label>
                                        <textarea name="meta_description" class="form-control" rows="3" placeholder="{{ __('admin.Meta Description') }}">{{ @$about->meta_description }}</textarea>
                                        <small class="form-text text-muted">{{ __('admin.Max 500 characters') }}</small>
                                    </div>


                                    <button type="submit" class="btn btn-primary">{{ __('admin.Save') }}</button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>


        </div>
    </section>
@endsection

@push('scripts')
    <script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Toast.fire({
                    icon: 'error',
                    title: "{{ $error }}"
                });
            @endforeach
        @endif

        if (jQuery().summernote) {
            @foreach ($languages as $language)
            $(".summernote-{{ $language->lang }}").summernote({
                dialogsInBody: true,
                minHeight: 300,
                maxHeight: 500,
                focus: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear', 'strikethrough', 'superscript', 'subscript']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                    ['height', ['height']]
                ],
                fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana', 'Nunito'],
                fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '36', '48', '64', '82', '150'],
                popover: {
                    image: [
                        ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                        ['float', ['floatLeft', 'floatRight', 'floatNone']],
                        ['remove', ['removeMedia']]
                    ],
                    link: [
                        ['link', ['linkDialogShow', 'unlink']]
                    ],
                    table: [
                        ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                        ['delete', ['deleteRow', 'deleteCol', 'deleteTable']]
                    ]
                }
            });
            @endforeach

        }
    </script>
@endpush
