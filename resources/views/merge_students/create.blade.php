@extends('layouts.index')
@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    @if(isset($rel))
                    ویرایش ارتباط
                    @else
                    ارتباط جدید
                    @endif

                </h1>
            </div>
            <div class="col-sm-6">
                <!--
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">DataTables</li>
          </ol>
          -->
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form method="POST" id="createForm">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="main">نام دانش‌ آموز اصلی</label>
                                    <select class="form-control" id="main" name="main">
                                        <option value="0"> - </option>
                                        @if(isset($rel))
                                        <option value="{{ ($rel->mainStudent) ? $rel->mainStudent->id : '-' }}" selected="selected">
                                            {{($rel->mainStudent) ? $rel->mainStudent->first_name : '-'}}
                                            {{($rel->mainStudent) ? $rel->mainStudent->last_name : ' '}}
                                            -{{($rel->mainStudent) ? $rel->mainStudent->phone : ' '}}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="auxilary">فرعی ۱</label>
                                    <select class="form-control" id="auxilary" name="auxilary">
                                        <option value="0"> - </option>
                                        @if(isset($rel))
                                        <option value="{{ ($rel->auxilaryStudent) ? $rel->auxilaryStudent->id : '-' }}" selected="selected">
                                            {{($rel->auxilaryStudent) ? $rel->auxilaryStudent->first_name : '-'}}
                                            {{($rel->auxilaryStudent) ? $rel->auxilaryStudent->last_name : ' '}}
                                            -{{($rel->auxilaryStudent) ? $rel->auxilaryStudent->phone : ' '}}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="second_auxilary">فرعی ۲</label>
                                    <select class="form-control" id="second_auxilary" name="second_auxilary">
                                        <option value="0"> - </option>
                                        @if(isset($rel))
                                        <option value="{{ ($rel->secondAuxilaryStudent) ? $rel->secondAuxilaryStudent->id : '-' }}" selected="selected">
                                            {{($rel->secondAuxilaryStudent) ? $rel->secondAuxilaryStudent->first_name : '-'}}
                                            {{($rel->secondAuxilaryStudent) ? $rel->secondAuxilaryStudent->last_name : ' '}}
                                            -{{($rel->secondAuxilaryStudent) ? $rel->secondAuxilaryStudent->phone : ' '}}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="third_auxilary">فرعی ۳</label>
                                    <select class="form-control" id="third_auxilary" name="third_auxilary">
                                        <option value="0"> - </option>
                                        @if(isset($rel))
                                        <option value="{{ ($rel->thirdAuxilaryStudent) ? $rel->thirdAuxilaryStudent->id : '-' }}" selected="selected">
                                            {{($rel->thirdAuxilaryStudent) ? $rel->thirdAuxilaryStudent->first_name : '-'}}
                                            {{($rel->thirdAuxilaryStudent) ? $rel->thirdAuxilaryStudent->last_name : ' '}}
                                            -{{($rel->thirdAuxilaryStudent) ? $rel->thirdAuxilaryStudent->phone : ' '}}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                @if(isset($rel))
                                <button class="btn btn-warning text-white">
                                    ویرایش
                                </button>
                                @else
                                <button class="btn btn-primary">
                                    ذخیره
                                </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

</section>
@endsection
@section('js')
<script src="/plugins/select2/js/select2.min.js"></script>
<script type="text/javascript">
    console.log($('#select2-main-results').children(":first"));
    function removeItem(first, second_option, third_option, forth_option) { 
        $(first).on('change', function() {
            var x = $(this).val();
            $(second_option).each(function() {
                if ($(this).val() == x) {
                    this.remove(x);
                }
            });
            $(third_option).each(function() {
                if ($(this).val() == x) {
                    this.remove(x);
                }
            });
            $(forth_option).each(function() {
                if ($(this).val() == x) {
                    this.remove(x);
                }
            });

        })
    }
    removeItem('#main', '#auxilary option', '#second_auxilary option', '#third_auxilary option');
    removeItem('#auxilary', '#main option', '#second_auxilary option', '#third_auxilary option');
    removeItem('#second_auxilary', '#main option', '#auxilary option', '#third_auxilary option');
    removeItem('#third_auxilary', '#main option', '#auxilary option', '#second_auxilary option');

</script>
<script type="text/javascript">
    function select2_load_remote_data_with_ajax(item) {
        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(item).select2({
            ajax: {
                url: "{{ route('merge_students_get') }}"
                , type: 'post'
                , dataType: 'json'
                , delay: 250
                , data: function(params) {
                    return {
                        _token: CSRF_TOKEN
                        , search: params.term
                    };
                }
                , processResults: function(response) {
                    return {
                        results: response
                    };
                }
                , cache: true
            }
            ,
            minimumInputLength: 3
        });
    }
    select2_load_remote_data_with_ajax('#main');
    select2_load_remote_data_with_ajax('#auxilary');
    select2_load_remote_data_with_ajax('#second_auxilary');
    select2_load_remote_data_with_ajax('#third_auxilary');

</script>
@endsection
