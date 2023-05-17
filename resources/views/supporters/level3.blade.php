@php
$majors = [
"mathematics"=>"ریاضی",
"experimental"=>"تجربی",
"humanities"=>"انسانی",
"art"=>"هنر",
"other"=>"دیگر"
];
$persons = [
"student"=>"دانش آموز",
"father"=>"پدر",
"mother"=>"مادر",
"other"=>"غیره"
];
$results = [
"no_answer"=>"بدون پاسخ",
"unsuccessful"=>"ناموفق",
"successful"=>"موفق",
"rejected"=>"در شده"
];
$egucation_levels = [
"13" => "فارغ التحصیل",
"14" => "دانشجو",
null => ""
];
@endphp
@extends('layouts.index')
@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
<link href="/dist/css/select2-style.css" rel="stylesheet" />

<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .morepanel {
        display: none;
    }

    div.dataTables_wrapper {
        width: 100% !important;
    }

    .cursor_pointer {
        cursor: pointer;
    }

</style>
<!-- Date Picker -->
<link href="/plugins/persiancalender/jquery.md.bootstrap.datetimepicker.style.css" rel="stylesheet" />
@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>دانش آموزان</h1>
                @if($user)
                <small>{{ $user->first_name }} {{ $user->last_name }}</small>
                @endif
            </div>
            <div class="col-sm-6">
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
                    <h3 class="card-title">
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <h3 class="text-center">
                        فیلتر
                    </h3>
                    <form method="post" id="search-frm">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="sources_id">منبع</label>
                                    <select id="sources_id" name="sources_id" class="form-control" onchange="return selectSources();">
                                        <option value="">همه</option>
                                        @foreach ($sources as $item)
                                        @if(isset($sources_id) && $sources_id==$item->id)
                                        <option value="{{ $item->id }}" selected>
                                            @else
                                        <option value="{{ $item->id }}">
                                            @endif
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="name">نام و نام خانوادگی</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="نام و نام خانوادگی" value="{{ isset($name)?$name:'' }}" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="phone">تلفن</label>
                                    <input type="number" class="form-control" id="phone" name="phone" placeholder="تلفن" value="{{ isset($phone)?$phone:'' }}" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="conditions">شروط</label>
                                    <select name="conditions" id="conditions" class="form-control" onchange="theSearch()">
                                        <option value="0">-</option>
                                        @foreach($saleSuggestions as $suggestion)
                                        <option value="{{$suggestion->id}}">{{$suggestion->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                               
                                <div class="form-group">
                                    <select id="has_product" class="form-control select2" multiple onchange="return selectProduct();">
                                        @if(isset($has_the_product[0]))
                                        <option value="" disabled >محصول</option>
                                        @else
                                        <option selected value="" disabled>محصول</option>
                                        @endif
                                        <option value="">همه</option>
                                        @foreach($products as $product)
                                        @if(isset($has_the_product[0]) && in_array($product->id,$has_the_product))
                                        <option  value="{{ $product->id }}">{{($product->parents!='-')?$product->parents . '->':''}} {{ $product->name }}</option>
                                        @else
                                        <option value="{{ $product->id }}">{{($product->parents!='-')?$product->parents . '->':''}} {{ $product->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                               
                               
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <select id="has_cal_result" class="form-control select2" multiple onchange="return selectCallResult();">
                                        @if(isset($has_call_result) && $has_call_result>0)
                                        <option value="" disabled>نتیجه تماس</option>
                                        @else
                                        <option selected value="" disabled>نتیجه تماس</option>
                                        @endif
                                        <option value="">همه</option>
                                        @foreach($callResults as $callResult)
                                        @if(isset($has_call_result) && $has_call_result==$callResult->id)
                                        <option value="{{ $callResult->id }}" selected>{{ $callResult->title }}</option>
                                        @else
                                        <option value="{{ $callResult->id }}">{{ $callResult->title }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                
                                <div class="form-group">
                                    <select id="has_the_tag" class="form-control select2" multiple onchange="return selectTag()">
                                        @if(isset($has_the_tags[0]))
                                        <option value="" disabled>برچسب اخلاقی</option>
                                        @else
                                        <option selected value="" disabled>برچسب اخلاقی</option>
                                        @endif
                                        <option value="">همه</option>
                                        @foreach($moralTags as $tag)
                                        @if(isset($has_the_tags[0]) && in_array($tag->id,$has_the_tags))
                                        <option value="{{ $tag->id }}" selected>{{ $tag->name }}</option>
                                        @else
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                               
                               
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <select id="has_need_tag" class="form-control select2" multiple onchange="return selectNeedTag()">
                                        @if(isset($has_the_tags[0]))
                                        <option value="" disabled>برچسب نیازسنجی</option>
                                        @else
                                        <option selected value="" disabled>برچسب نیازسنجی</option>
                                        @endif
                                        <option value="">همه</option>
                                        @foreach($needTags as $tag)
                                        @if(isset($has_the_tags[0]) && in_array($tag->id,$has_the_tags))
                                        <option value="{{ $tag->id }}" selected>{{ $tag->name }}</option>
                                        @else
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- <div class="col text-center p-1">
                                <button class="btn btn-primary">همه</button>
                            </div> --}}
                            <div class="col text-center p-1">
                                <select id="education_level" class="form-control select2" onchange="return selectEducationLevels();">
                                    <option selected value="" disabled>مقطع</option>
                                    <option value="">همه</option>
                                    @for($i = 6;$i < 15;$i++) @if(isset($egucation_levels[$i])) <option value="{{ $i }}">
                                        {{ $egucation_levels[$i] }}</option>
                                        @else
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endif
                                        @endfor
                                </select>
                        </div>
                        <div class="col text-center p-1">
                                <select id="major" class="form-control select2" onchange="return selectMajors();">
                                    <option selected value="" disabled>رشته</option>
                                    <option value="">همه</option>
                                    @foreach($majors as $item => $value)
                                    //@if(isset($item))
                                    <option value="{{ $item }}">{{ $value }}</option>
                                    //@else
                                    //<option value="{{ $item }}">{{ $item }}</option>
                                    //@endif
                                    @endforeach
                                </select>
                        </div>
                            <div class="col">
                                <a class="btn btn-success" onclick="theSearch()" href="#">
                                    جستجو
                                </a>
                                <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
                            </div>
                        </div>
                        <input type="hidden" id="has_collection" name="has_collection" value="{{ isset($has_collection)?$has_collection:'false' }}" />
                        <input type="hidden" id="has_the_product" name="has_the_product" value="{{ isset($has_the_product[0])?implode(',', $has_the_product):'' }}" />
                        <input type="hidden" id="has_the_tags" name="has_the_tags" value="{{ isset($has_the_tags[0])?implode(',', $has_the_tags):'' }}" />
                        <input type="hidden" id="has_call_result" name="has_call_result" value="{{ isset($has_call_result)?$has_call_result:'' }}" />
                        <input type="hidden" id="has_site" name="has_site" value="{{ isset($has_site)?$has_site:'false' }}" />
                        <input type="hidden" id="order_collection" name="order_collection" value="{{ isset($order_collection)?$order_collection:'false' }}" />
                        <input type="hidden" id="has_reminder" name="has_reminder" value="{{ isset($has_reminder)?$has_reminder:'false' }}" />
                        <input type="hidden" id="has_tag" name="has_tag" value="{{ isset($has_tag)?$has_tag:'false' }}" />
                        <input type="hidden" id="has_need_tags" name="has_need_tags" value="{{ isset($has_the_tags[0])?implode(',', $has_the_tags):'' }}" />
                    </form>
                    <h3 class="text-center">
                        مرتب سازی
                    </h3>
                    <div class="row">
                        <div class="col text-center p-1">
                            @if(isset($has_collection) && $has_collection=='true')
                            <a id="student-collection-btn" class="btn btn-success btn-block" href="#" onclick="return StudentCollection();">پیشنهاد فروش</a>
                            @else
                            <a id="student-collection-btn" class="btn btn-warning btn-block" href="#" onclick="return StudentCollection();">پیشنهاد فروش</a>
                            @endif
                        </div>
                        <div class="col text-center p-1">
                            @if(isset($has_site) && $has_site=='true')
                            <a id="student-site-btn" class="btn btn-success btn-block" href="#" onclick="return StudentSite();">سایت</a>
                            @else
                            <a id="student-site-btn" class="btn btn-warning btn-block" href="#" onclick="return StudentSite();">سایت</a>
                            @endif
                        </div>
                        <div class="col text-center p-1">
                            @if(isset($order_collection) && $order_collection=='true')
                            <a id="order-collection-btn" class="btn btn-success btn-block" href="#" onclick="return OrderCollection();">تعداد پیشنهاد فروش</a>
                            @else
                            <a id="order-collection-btn" class="btn btn-warning btn-block" href="#" onclick="return OrderCollection();">تعداد پیشنهاد فروش</a>
                            @endif
                        </div>
                        <div class="col text-center p-1">
                            @if(isset($has_reminder) && $has_reminder=='true')
                            <a id="student-reminder-btn" class="btn btn-success btn-block" href="#" onclick="return StudentReminder();">یادآور</a>
                            @else
                            <a id="student-reminder-btn" class="btn btn-warning btn-block" href="#" onclick="return StudentReminder();">یادآور</a>
                            @endif
                        </div>
                    </div>
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr class="table_header">
                                <th>ردیف</th>
                                <th>کد</th>
                                <th>نام</th>
                                <th>نام خانوادگی</th>
                                <th>کاربر ثبت کننده</th>
                                <th>منبع ورودی شماره</th>
                                <th>برچسب</th>
                                <th>داغ/سرد</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@endsection

@section('js')
<div class="modal" id="tag_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">برچسب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <input type="hidden" id="students_index" />
                    <div class="morals">
                        <h3 class="text-center">
                            اخلاقی
                        </h3>
                        <div>
                            <select id="parent-one" onchange="selectParentOne(this,0);">
                                <option value="">همه</option>
                                @foreach ($parentOnes as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>

                            <select id="parent-two" onchange="selectParentTwo(this,0);">
                                <option value="">همه</option>
                                @foreach ($parentTwos as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>

                            <select id="parent-three" onchange="selectParentThree(this,0);">
                                <option value="">همه</option>
                                @foreach ($parentThrees as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>

                            <select id="parent-four" onchange="selectParentFour(this,0);">
                                <option value="">همه</option>
                                @foreach ($parentFours as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @foreach ($moralTags as $index => $item)
                        <input type="checkbox" class="tag-checkbox" id="tag_{{ $item->id }}" value="{{ $item->id }}" />
                        <span class="tag-title" id="tag-title-{{ $item->id }}">
                            {{ $item->name }}
                        </span>
                        <br class="tag-br" id="tag-br-{{ $item->id }}" />
                        @endforeach
                    </div>
                    <div class="needs">
                        <h3 class="text-center">
                            نیازسنجی
                        </h3>
                        <div>
                            <select id="need-parent-one" onchange="selectNeedParentOne(this);">
                                <option value="">همه</option>
                                @foreach ($needTagParentOnes as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>

                            <select id="need-parent-two" onchange="selectNeedParentTwo(this);">
                                <option value="">همه</option>
                                @foreach ($needTagParentTwos as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>

                            <select id="need-parent-three" onchange="selectNeedParentThree(this);">
                                <option value="">همه</option>
                                @foreach ($needTagParentThrees as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>

                            <select id="need-parent-four" onchange="selectNeedParentFour(this);">
                                <option value="">همه</option>
                                @foreach ($needTagParentFours as $item)
                                <option value="{{ $item->id }}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @foreach ($needTags as $index => $item)
                        <input type="checkbox" class="needtag-checkbox" id="needtag_{{ $item->id }}" value="{{ $item->id }}" />
                        <span class="needtag-title" id="needtag-title-{{ $item->id }}">
                            {{ $item->name }}
                        </span>
                        <br class="needtag-br" id="needtag-br-{{ $item->id }}" />
                        @endforeach
                    </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveTags();">اعمال</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="tag_modal_filter" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">برچسب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <input type="hidden" id="students1_index" />
                    <h3 class="text-center">
                        اخلاقی
                    </h3>
                    <div>
                        <select id="parent1" onchange="selectParentOne(this,1);">
                            <option value="">همه</option>
                            @foreach ($parentOnes as $item)
                            <option value="{{ $item->id }}">{{$item->name}}</option>
                            @endforeach
                        </select>

                        <select id="parent2" onchange="selectParentTwo(this,1);">
                            <option value="">همه</option>
                            @foreach ($parentTwos as $item)
                            <option value="{{ $item->id }}">{{$item->name}}</option>
                            @endforeach
                        </select>

                        <select id="parent3" onchange="selectParentThree(this,1);">
                            <option value="">همه</option>
                            @foreach ($parentThrees as $item)
                            <option value="{{ $item->id }}">{{$item->name}}</option>
                            @endforeach
                        </select>

                        <select id="parent4" onchange="selectParentFour(this,1);">
                            <option value="">همه</option>
                            @foreach ($parentFours as $item)
                            <option value="{{ $item->id }}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @foreach ($moralTags as $index => $item)
                    <input type="checkbox" class="filter-tag-checkbox" id="filter-tag_{{ $item->id }}" value="{{ $item->id }}" />
                    <span class="tag1-title" id="tag1-title-{{ $item->id }}">
                        {{ $item->name }}
                    </span>
                    <br class="tag1-br" id="tag1-br-{{ $item->id }}" />
                    @endforeach
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveFilterTags();">اعمال</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="temperature_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">داغ/سرد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <input type="hidden" id="students_index2" />
                    <h3 class="text-center">
                        داغ
                    </h3>
                    @foreach ($hotTemperatures as $index => $item)
                    <input type="checkbox" class="temperature-checkbox" id="temperature_{{ $item->id }}" value="{{ $item->id }}" />
                    {{ $item->name }}
                    @endforeach
                    <h3 class="text-center">
                        سرد
                    </h3>
                    @foreach ($coldTemperatures as $index => $item)
                    <input type="checkbox" class="temperature-checkbox" id="temperature_{{ $item->id }}" value="{{ $item->id }}" />
                    {{ $item->name }}
                    @endforeach
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveTemperatures();">اعمال</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="call_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ثبت تماس</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" id="successfullCall" style="display:none"></div>
                <p>
                    <div class="form-group">
                        <label for="call_results_id">نتیجه</label>
                        <select class="form-control" id="call_results_id" name="call_results_id">
                            @foreach ($callResults as $item)
                            <option value="{{ $item->id }}" data-nocall="{{ $item->no_call }}" data-noanswer="{{ $item->no_answer }}" {{ $call_results_id == $item->id ? 'selected' : '' }}>
                                {{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="replier">پاسخگو</label>
                        <select class="form-control" id="replier" name="replier">
                            <option value="student" {{ $replier == "student" ? 'selected' : '' }}>دانش آموز</option>
                            <option value="father" {{ $replier == "father" ? 'selected' : '' }}>پدر</option>
                            <option value="mother" {{ $replier == "mother" ? 'selected' : '' }}>مادر</option>
                            <option value="other" {{ $replier == "other" ? 'selected' : '' }}>غیره</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="products_id">محصول</label>
                        <select class="form-control select2" id="products_id" name="products_id[]" style="width: 100% !important;" multiple>
                            <option value=""></option>
                            @foreach ($products as $item)
                            <option value="{{ $item->id }}" {{ $products_id == $item->id ? 'selected' : '' }}>
                                {{($item->parents!='-')?$item->parents . '->':''}} {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="products_id">اطلاع رسانی</label>
                        <select class="form-control select2" id="notices_id" name="notices_id" style="width: 100% !important;">
                            <option value=""></option>
                            @foreach ($notices as $item)
                            <option value="{{ $item->id }}" {{ $notices_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="next_to_call">تماس بعد</label>
                        <select class="form-control" id="next_to_call" name="next_to_call">
                            <option value="student" {{ $next_to_call == "student" ? 'selected' : '' }}>دانش آموز</option>
                            <option value="father" {{ $next_to_call == "father" ? 'selected' : '' }}>پدر</option>
                            <option value="mother" {{ $next_to_call == "mother" ? 'selected' : '' }}>مادر</option>
                            <option value="other" {{ $next_to_call == "other" ? "selected" : '' }}>غیره</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">زمان تماس بعد</label>
                        <a class="btn btn-link" onclick="$('#next_call_persian').val('');$('#next_call').val('');return false;" href="#">خالی کردن</a>
                        <input type="text" class="form-control" id="next_call_persian" placeholder="زمان تماس بعد" readonly />
                        <input type="hidden" id="next_call" name="next_call" />
                    </div>
                    <div class="form-group">
                        <label for="description">توضیحات</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="توضیحات" />
                    </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveCall();">ثبت</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            </div>
        </div>
    </div>
</div>
<!-- Date Picker -->
<script src="/plugins/persiancalender/jquery.md.bootstrap.datetimepicker.js"></script>
<!-- Select2 -->
<script src="/plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<!-- <script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script> -->

<script src="../../plugins/DataTables/DataTables-1.13.4/js/jquery.dataTables.js"></script>  
<script src="../../plugins/DataTables/DataTables-1.13.4/js/dataTables.bootstrap4.js"></script>  

<script type="text/javascript">
    var studentMergeData = ``;
    var first = ``;
    var second = ``;
    var third = ``;
    var forth = ``;

    function mergeStudents(item, selfId) {
        studentMergeData = ``;
        first = ``;
        second = ``;
        third = ``;
        forth = ``;
        if (item) {
            if (selfId != item.main_student.id) {
                first = `<p class="cursor_pointer text-success" id="${item.main_student.id}"
                onclick="searchMain(${item.main_student.id})">
                    ${item.main_student.first_name}
                    ${item.main_student.last_name}
                    [${item.main_student.phone}]</p>`;
            }
            if (item.auxilary_student && selfId != item.auxilary_student.id) {
                second = `<p id="${item.auxilary_student.id}">
                    ${item.auxilary_student.first_name}
                    ${item.auxilary_student.last_name}
                    [${item.auxilary_student.phone}]</p>`;
            }
            if (item.second_auxilary_student && selfId != item.second_auxilary_student.id) {
                third = `<p id="${item.second_auxilary_student.id}">
                ${item.second_auxilary_student.first_name}
                ${item.second_auxilary_student.last_name}
                [${item.second_auxilary_student.phone}]</p>`;
            }
            if (item.third_auxilary_student && selfId != item.third_auxilary_student.id) {
                forth = `<p id="${item.third_auxilary_student.id}">${item.third_auxilary_student.first_name}
                ${item.third_auxilary_student.last_name}
                [${item.third_auxilary_student.phone}]</p>`;
            }
            studentMergeData = `افراد مرتبط:<br>` + first + second + third + forth;
        }
        return studentMergeData;
    }

</script>
<!-- page script -->
<script>
    let lastPage = 1;
    let isSearchCall= false;
    let students = @JSON($students);
    let parentOnes = @JSON($parentOnes);
    let parentTwos = @JSON($parentTwos);
    let parentThrees = @JSON($parentThrees);
    let parentFours = @JSON($parentFours);
    let tmpTags = @JSON($moralTags);
    let tmpCollections = @JSON($needTags);
    let egucation_levels = @JSON($egucation_levels);
    let majors = @JSON($majors);
    let tags = {};
    let collections = {};
    let calls_id = {{isset($calls_id) ? $calls_id : 'null'}};
    var table;
    for (let tg of tmpTags) {
        tags[tg.id] = tg;
    }
    for (let cl of tmpCollections) {
        collections[cl.id] = cl;
    }
    let filterParents = {
        parent1: ''
        , parent2: ''
        , parent3: ''
        , parent4: ''
        , need_parent1: ''
        , need_parent2: ''
        , need_parent3: ''
        , need_parent4: ''
    }
    let students_id = {{isset($students_id) ? $students_id : 'null'}};

    function showMorePanel(index, tr) {
        var persons = {
            student: "دانش آموز"
            , father: "پدر"
            , mother: "مادر"
            , other: "غیره"
        };
        var editRoute = `{{ route('student_edit', ['call_back'=>'supporter_students', 'id'=>-1]) }}`;
        var purchaseRoute = `{{ route('student_purchases', -1) }}`;
        var supporterStudentAllCallRoute = `{{ route('supporter_student_allcall', -1) }}`;
        var tmpCallWithRecall = `
            <tr>
                <td>#index#</td>
                <td>#id#</td>
                <td>#product#</td>
                <td>#notice#</td>
                <td>#replier#</td>
                <td>#callresult#</td>
                <td>#next_call#</td>
                <td>#next_to_call#</td>
                <td>#description#</td>
                <td>
                    <a class="btn btn-primary" href="#" onclick="calls_id = #id#;students_id = #students_id#;$('#call_modal').modal('show');return false;">
                        ثبت تماس
                    </a>
                </td>
            </tr>`;
        var tmpCall = `
            <tr>
                <td>#index#</td>
                <td>#id#</td>
                <td>#product#</td>
                <td>#notice#</td>
                <td>#replier#</td>
                <td>#callresult#</td>
                <td>#next_call#</td>
                <td>#next_to_call#</td>
                <td>#description#</td>
                <td></td>
            </tr>`;
        var calls = '';
        var callIndex = 1;
        for (var call of students[index].calls) {
            if (callIndex <= 5) {
                @if(isset($students_id) && $students_id != null)
                calls += tmpCall.replace('#index#', callIndex)
                    .replace(/#id#/g, call.id)
                    .replace('#product#', (call.product) ? call.product.name : '-')
                    .replace('#notice#', (call.notice) ? call.notice.name : '-')
                    .replace('#replier#', persons[call.replier])
                    .replace('#callresult#', (call.callresult) ? call.callresult.title : '-')
                    .replace('#next_call#', (call.next_call) ? call.next_call : '-')
                    .replace('#next_to_call#', persons[call.next_to_call])
                    .replace('#students_id#', call.students_id)
                    .replace('#description#', (call.description) ? call.description : '-');
                @else
                if (call.next_call != "-") {
                    calls += tmpCallWithRecall.replace('#index#', callIndex)
                        .replace(/#id#/g, call.id)
                        .replace('#product#', (call.product) ? call.product.name : '-')
                        .replace('#notice#', (call.notice) ? call.notice.name : '-')
                        .replace('#replier#', persons[call.replier])
                        .replace('#callresult#', (call.callresult) ? call.callresult.title : '-')
                        .replace('#next_call#', (call.next_call) ? call.next_call : '-')
                        .replace('#next_to_call#', persons[call.next_to_call])
                        .replace('#students_id#', call.students_id)
                        .replace('#description#', (call.description) ? call.description : '-');

                } else {
                    calls += tmpCall.replace('#index#', callIndex)
                        .replace(/#id#/g, call.id)
                        .replace('#product#', (call.product) ? call.product.name : '-')
                        .replace('#notice#', (call.notice) ? call.notice.name : '-')
                        .replace('#replier#', persons[call.replier])
                        .replace('#callresult#', (call.callresult) ? call.callresult.title : '-')
                        .replace('#next_call#', (call.next_call) ? call.next_call : '-')
                        .replace('#next_to_call#', persons[call.next_to_call])
                        .replace('#students_id#', call.students_id)
                        .replace('#description#', (call.description) ? call.description : '-');
                }
                @endif
            } else {
                continue;
            }
            callIndex++;
        }

        var merge = students[index].mergestudent;
        var stu_id = students[index].id;
        var auxilaryMerge = students[index].mergeauxilarystudent;
        var secondAuxilaryMerge = students[index].mergesecondauxilarystudent;
        var thirdAuxilaryMerge = students[index].mergethirdauxilarystudent;
        var x = mergeStudents(merge, stu_id);
        var y = mergeStudents(auxilaryMerge, stu_id);
        var z = mergeStudents(secondAuxilaryMerge, stu_id);
        var t = mergeStudents(thirdAuxilaryMerge, stu_id);
        var test = `<table style="width: 100%">
            <tr>
                <td>
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                تراز یا رتبه سال قبل :
                                ${ (students[index].last_year_grade!=null)?students[index].last_year_grade:'' }
                            </div>
                            <div class="col">
                                مشاور :
                                ${ (students[index].consultant)?students[index].consultant.first_name + ' ' + students[index].consultant.last_name:'' }
                            </div>
                            <div class="col">
                                شغل پدر یا مادر :
                                ${ (students[index].parents_job_title!=null)?students[index].parents_job_title:'' }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                شماره منزل :
                                ${ (students[index].home_phone!=null)?students[index].home_phone:'' }
                            </div>
                            <div class="col">
                                مقطع :
                                ${ (students[index].egucation_level!=null)?((egucation_levels[students[index].egucation_level])?egucation_levels[students[index].egucation_level]:students[index].egucation_level):'' }
                            </div>
                            <div class="col">
                                شماره موبایل والدین :
                                ${ (students[index].father_phone!=null)?students[index].father_phone:'' }
                                ${ (students[index].mother_phone!=null)?students[index].mother_phone:'' }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                مدرسه :
                                ${ (students[index].school)!=null?students[index].school:'' }
                            </div>
                            <div class="col">
                                معدل :
                                ${ (students[index].average!=null)?students[index].average:'' }
                            </div>
                            <div class="col">
                                رشته تحصیلی :
                                ${ (majors[students[index].major])?majors[students[index].major]:'-' }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                تلفن دانش آموز:
                                ${students[index].phone}
                            </div>
                            <div class="col">
                                تاریخ ثبت دانش آموز :
                                ${ students[index].pcreated_at }
                            </div>

                            <div class="col">
                               ${x} ${y}  ${z}  ${t}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="${ editRoute.replace('-1', students[index].id) }" target="_blank">
                                    ویرایش مشخصات
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="#" onclick="$('#students_index').val(${ index });preloadTagModal('moral');$('#tag_modal').modal('show'); return false;">
                                    برچسب روحیات اخلاقی
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="#" onclick="$('#students_index').val(${ index });preloadTagModal('need');$('#tag_modal').modal('show'); return false;">
                                    برچسب نیازهای دانش آموز
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a target="_blank" href="${ purchaseRoute.replace('-1', students[index].id) }">
                                    گزارش خریدهای قطعی دانش آموز
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a target="_blank" href="${ supporterStudentAllCallRoute.replace('-1', students[index].id) }">
                                    گزارش تماس ها
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a class="btn btn-success" href="#" onclick="calls_id = {{(isset($students_id) && $students_id!=null)?$calls_id:'null'}};students_id = ${ students[index].id };$('#call_modal').modal('show');return false;">
                                    ثبت تماس
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-bordered table-hover datatables-all datatables" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ردیف</th>
                                        <th>کد</th>
                                        <th>محصول</th>
                                        <th>اطلاع رسانی</th>
                                        <th>پاسخگو</th>
                                        <th>نتیجه</th>
                                        <th>یادآور</th>
                                        <th>پاسخگو بعد</th>
                                        <th>توضیحات</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${ calls }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>`;

        var row = table.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
        } else {
            row.child(test).show();
        }
    }
    
    let theMain = null;
    let theAuxilary = null;
    let theSecondAuxilary = null;
    let theThirdAuxilary = null;
    searchMain = (id) => {
        theMain = id;
        theAuxilary = null;
        theSecondAuxilary = null;
        theThirdAuxilary = null;
        table.ajax.reload();
    };
    searchAuxilary = (id) => {
        theMain = null;
        theAuxilary = id;
        theSecondAuxilary = null;
        theThirdAuxilary = null;
        table.ajax.reload();
    };
    searchSecondAuxilary = (id) => {
        theMain = null;
        theAuxilary = null;
        theSecondAuxilary = id;
        theThirdAuxilary = null;
        table.ajax.reload();
    };
    searchThirdAuxilary = (id) => {
        theMain = null;
        theAuxilary = null;
        theSecondAuxilary = null;
        theThirdAuxilary = id;
        table.ajax.reload();
    }

    function emptySomeData() {
        theMain = null;
        theAuxilary = null;
        theSecondAuxilary = null;
        theThirdAuxilary = null;
    }


    function changeSupporter(studentsIndex, id) {
        if (students[studentsIndex]) {
            var students_id = id;
            var supporters_id = $("#supporters_id_" + studentsIndex).val();
            $("#loading-" + studentsIndex).show();
            $.post('{{ route('student_supporter') }}', {
                    students_id
                    , supporters_id
                }
                , function(result) {
                    $("#loading-" + studentsIndex).hide();
                    if (result && result.error != null) {
                        alert(result.error);
                    }
                    table.ajax.reload();
                }).fail(function() {
                $("#loading-" + studentsIndex).hide();
                alert('خطای بروز رسانی');
                table.ajax.reload();
            });
        }
        return false;
    }

    function theFilterTags(sw) {
        if (!sw) {
            filterTagsByParent("input.tag-checkbox", "span.tag-title", "br.tag-br", "#tag-title-", "#tag-br-");
        } else {
            filterTagsByParent("input.filter-tag-checkbox", "span.tag1-title", "br.tag1-br", "#tag1-title-", "#tag1-br-");
        }
    }

    function selectParentOne(dobj, sw) {
        filterParents.parent1 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        theFilterTags(sw);
    }

    function selectParentTwo(dobj, sw) {
        filterParents.parent2 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        theFilterTags(sw);
    }

    function selectParentThree(dobj, sw) {
        filterParents.parent3 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        theFilterTags(sw);
    }

    function selectParentFour(dobj, sw) {
        filterParents.parent4 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        theFilterTags(sw);
    }

    function filterTagsByParent(theCheckbox, title, br, title_id, br_id) {
        $(theCheckbox).show();
        $(title).show();
        $(br).show();
        $(theCheckbox).each(function(id, field) {
            let tagId = parseInt($(field).val(), 10);
            let theTag = tags[tagId];
            if (theTag) {
                if (filterParents.parent1 != '') {
                    if (filterParents.parent1 != theTag.parent1) {
                        $(field).hide();
                        $(title_id + tagId).hide();
                        $(br_id + tagId).hide();
                    }
                }
                if (filterParents.parent2 != '') {
                    if (filterParents.parent2 != theTag.parent2) {
                        $(field).hide();
                        $(title_id + tagId).hide();
                        $(br_id + tagId).hide();
                    }
                }
                if (filterParents.parent3 != '') {
                    if (filterParents.parent3 != theTag.parent3) {
                        $(field).hide();
                        $(title_id + tagId).hide();
                        $(br_id + tagId).hide();
                    }
                }
                if (filterParents.parent4 != '') {
                    if (filterParents.parent4 != theTag.parent4) {
                        $(field).hide();
                        $(title_id + tagId).hide();
                        $(br_id + tagId).hide();
                    }
                }
            }
        });
    }

    function selectNeedParentOne(dobj) {
        filterParents.need_parent1 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        filterNeedTagsByParent()
    }

    function selectNeedParentTwo(dobj) {
        filterParents.need_parent2 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        filterNeedTagsByParent()
    }

    function selectNeedParentThree(dobj) {
        filterParents.need_parent3 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        filterNeedTagsByParent()
    }

    function selectNeedParentFour(dobj) {
        filterParents.need_parent4 = ($(dobj).val() != '') ? parseInt($(dobj).val(), 10) : '';
        filterNeedTagsByParent()
    }

    function filterNeedTagsByParent() {
        $("input.needtag-checkbox").show();
        $("span.needtag-title").show();
        $("br.needtag-br").show();
        $("input.needtag-checkbox").each(function(id, field) {
            console.log('checking', field)
            let tagId = parseInt($(field).val(), 10);
            let theTag = collections[tagId];
            console.log(tagId, theTag)
            if (theTag) {
                if (filterParents.need_parent1 != '') {
                    if (filterParents.need_parent1 != theTag.need_parent1) {
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
                if (filterParents.need_parent2 != '') {
                    if (filterParents.need_parent2 != theTag.need_parent2) {
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
                if (filterParents.need_parent3 != '') {
                    if (filterParents.need_parent3 != theTag.need_parent3) {
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
                if (filterParents.need_parent4 != '') {
                    if (filterParents.need_parent4 != theTag.need_parent4) {
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
            }

        });
    }

    function selectCollectionOne(dobj) {
        $("#collection-two").find('option').show();
        $("#collection-two").find('option[value=""]').prop('selected', true);
        if ($(dobj).val() != '') {
            $("#collection-two").find('option').each(function(id, field) {
                if ($(field).data('parent_id') != $(dobj).val() && $(field).val() != '') {
                    $(field).hide();
                } else {
                    $(field).show();
                }
            });
        }
        $("#collection-three").find('option').show();
        $("#collection-three").find('option[value=""]').prop('selected', true);
        if ($(dobj).val() != '') {
            $("#collection-three").find('option').each(function(id, field) {
                if ($(field).data('parent_parent_id') != $(dobj).val()) {
                    $(field).hide();
                } else {
                    $(field).show();
                }
            });
        }
        filterCollectionsByParent();
    }

    function selectCollectionTwo(dobj) {
        console.log('hey');
        $("#collection-three").find('option').show();
        $("#collection-three").find('option[value=""]').prop('selected', true);
        if ($(dobj).val() != '') {
            $("#collection-three").find('option').each(function(id, field) {
                if ($(field).data('parent_id') != $(dobj).val()) {
                    $(field).hide();
                } else {
                    $(field).show();
                }
            });
        }
        filterCollectionsByParent();
    }

    function selectCollectionThree(dobj) {
        console.log('hey3');
        filterCollectionsByParent();
    }

    function filterCollectionsByParent() {
        $("input.collection-checkbox").show();
        $("span.collection-title").show();
        $("br.collection-br").show();
        let collectionParents = $("#collection-two").val();
        let parents = [];
        if ($("#collection-one").val() == '' && collectionParents == '') {
            return false;
        }




        if (collectionParents == '') {
            parents.push(parseInt($("#collection-one").val(), 10));
            $("#collection-two").find('option').each(function(id, field) {
                if ($(field).css('display') != 'none' && !isNaN(parseInt($(field).val(), 10))) {
                    parents.push(parseInt($(field).val(), 10));
                }
            });
            console.log('p1', parents);
            $("input.collection-checkbox").each(function(id, field) {
                let collectionId = parseInt($(field).val(), 10);
                if (parents.indexOf(collectionId) < 0) {
                    $(field).hide();
                    $("#collection-title-" + collectionId).hide();
                    $("#collection-br-" + collectionId).hide();
                }
            });
            return false;
        } else {
            parents.push(parseInt(collectionParents, 10))
        }


        if ($("#collection-three").val() == '') {
            parents.push(parseInt($("#collection-two").val(), 10));
            $("#collection-three").find('option').each(function(id, field) {
                if ($(field).css('display') != 'none' && !isNaN(parseInt($(field).val(), 10))) {
                    parents.push(parseInt($(field).val(), 10));
                }
            });
            console.log('p2', parents);
            $("input.collection-checkbox").each(function(id, field) {
                let collectionId = parseInt($(field).val(), 10);
                if (parents.indexOf(collectionId) < 0) {
                    $(field).hide();
                    $("#collection-title-" + collectionId).hide();
                    $("#collection-br-" + collectionId).hide();
                }
            });
            return false;
        } else {
            parents.push(parseInt($("#collection-three").val(), 10))
        }

        console.log('parents:', parents);

        $("input.collection-checkbox").each(function(id, field) {
            let collectionId = parseInt($(field).val(), 10);
            let theCollection = collections[collectionId];
            console.log(collectionId, theCollection)
            if (theCollection) {
                console.log(parents.indexOf(theCollection.id), parents.indexOf(theCollection.parent_id));
                if (parents.indexOf(theCollection.id) < 0 && parents.indexOf(theCollection.parent_id) < 0) {
                    $(field).hide();
                    $("#collection-title-" + collectionId).hide();
                    $("#collection-br-" + collectionId).hide();
                }
            }

        });
    }

    function preloadFilterTagModal() {
        $("input.filter-tag-checkbox").prop('checked', false);
        var selecteds = $("#has_the_tag").val();
        console.log(selecteds);
        for (var i of selecteds) {
            if (i == '') {
                $("input.filter-tag-checkbox").prop('checked', true);
            }
            $("#filter-tag_" + i).prop('checked', true);
        }
    }

    function saveFilterTags() {
        emptySomeData();
        console.log('SAVE')
        $(`#has_the_tag option`).prop('selected', false);
        $("input.filter-tag-checkbox:checked").each(function(id, field) {
            console.log(`#has_the_tag option[value='${$(field).val()}']`);
            $(`#has_the_tag option[value='${$(field).val()}']`).prop('selected', true);
        });
        $('#tag_modal_filter').modal('hide');
        console.log($("#has_the_tag").val());
        $("#has_the_tags").val($("#has_the_tag").val().join(','));
        table.ajax.reload();
    }

    function selectFilterAll() {
        var state = $("#filter-tag_all").prop('checked');
        console.log('State', state);
        $("input.filter-tag-checkbox").each(function(id, field) {
            console.log(field);
            if (field.id != "filter-tag_all") {
                console.log('set', field.id, state);
                $(field).prop('checked', state);
            }
        });
    }

    function preloadTagModal(mode) {
        if (mode == 'need') {
            $("div.needs").show();
            $("div.morals").hide();
        } else {
            $("div.needs").hide();
            $("div.morals").show();
        }
        $("input.tag-checkbox").prop('checked', false);
        $("input.collection-checkbox").prop('checked', false);
        var studentsIndex = parseInt($("#students_index").val(), 10);
        if (!isNaN(studentsIndex)) {
            if (students[studentsIndex]) {
                console.log(students[studentsIndex].studenttags);
                for (studenttag of students[studentsIndex].studenttags) {
                    $("#tag_" + studenttag.tags_id).prop("checked", true);
                    $("#needtag_" + studenttag.tags_id).prop("checked", true);
                }
                console.log(students[studentsIndex].studentcollections);
                for (studentcollection of students[studentsIndex].studentcollections) {
                    $("#collection_" + studentcollection.collections_id).prop("checked", true);
                }

            }
        }
    }

    function preloadTemperatureModal() {
        $("input.tag-checkbox").prop('checked', false);
        var studentsIndex = parseInt($("#students_index2").val(), 10);
        if (!isNaN(studentsIndex)) {
            if (students[studentsIndex]) {
                console.log(students[studentsIndex].studenttemperatures);
                for (studenttag of students[studentsIndex].studenttemperatures) {
                    $("#temperature_" + studenttag.temperatures_id).prop("checked", true);
                }
            }
        }
    }

    function saveTags() {
        emptySomeData();
        var selectedTags = [];
        var selectedColllections = [];
        $("input.tag-checkbox:checked").each(function(id, field) {
            selectedTags.push(parseInt(field.value, 10));
        });
        $("input.needtag-checkbox:checked").each(function(id, field) {
            selectedColllections.push(parseInt(field.value, 10));
        });
        var studentsIndex = parseInt($("#students_index").val(), 10);
        if (!isNaN(studentsIndex)) {
            if (students[studentsIndex]) {
                console.log('selected tags', selectedTags);
                console.log('selected collections', selectedColllections);
                $.post('{{ route('student_tag') }}', {
                        students_id: students[studentsIndex].id
                        , selectedTags
                        , selectedColllections
                    }
                    , function(result) {
                        console.log('Result', result);
                        if (result.error != null) {
                            alert('خطای بروز رسانی');
                        } else {
                            table.ajax.reload();
                        }
                    }).fail(function() {
                    alert('خطای بروز رسانی');
                });
            }
        }
    }

    function saveTemperatures() {
        emptySomeData();
        var selectedTemperatures = [];
        $("input.temperature-checkbox:checked").each(function(id, field) {
            selectedTemperatures.push(parseInt(field.value, 10));
        });
        var studentsIndex = parseInt($("#students_index2").val(), 10);
        if (!isNaN(studentsIndex)) {
            if (students[studentsIndex]) {
                console.log('selected temperatures', selectedTemperatures);
                $.post('{{ route('student_temperature') }}', {
                        students_id: students[studentsIndex].id
                        , selectedTemperatures
                    }
                    , function(result) {
                        console.log('Result', result);
                        if (result.error != null) {
                            alert('خطای بروز رسانی');
                        } else {
                            table.ajax.reload();
                            //window.location.reload();
                        }
                    }).fail(function() {
                    alert('خطای بروز رسانی');
                });
            }
        }
    }

    function GoBackWithRefresh(event) {
        if ('referrer' in document) {
            window.location = document.referrer;
            /* OR */
            //location.replace(document.referrer);
        } else {
            window.history.back();
        }
    }

    $('#modal').on('click', function() {
        $('#successfullCall').css('display', 'none');
    });

    function saveCall() {
        const canSaveWithNoAlert = ($("#call_results_id option:selected").data('nocall') /* + $("#call_results_id option:selected").data('noanswer')*/ ) > 0;
        if (!$("#next_call").val() && !canSaveWithNoAlert) {
            alert('ثبت بدون یادآور ممنوع می باشد');
            return;
        }
        if (canSaveWithNoAlert && $("#next_call").val() != '') {
            alert('ثبت بدون یادآور باید باشد');
            return;
        }
        if ($("#notices_id").val() == '' && $("#products_id").val().length == 0) {
            alert('انتخاب محصول یا اطلاع رسانی لازم است');
            return;
        }
        console.log('request', {
            students_id
            , description: $("#description").val()
            , result: $("#result").val()
            , replier: $("#replier").val()
            , products_id: $("#products_id").val()
            , notices_id: $("#notices_id").val()
            , next_to_call: $("#next_to_call").val()
            , next_call: $("#next_call").val()
            , call_results_id: $("#call_results_id").val()
            , calls_id
        });
        $.post('{{ route('supporter_student_call') }}', {
                students_id
                , description: $("#description").val()
                , result: $("#result").val()
                , replier: $("#replier").val()
                , products_id: $("#products_id").val()
                , notices_id: $("#notices_id").val()
                , next_to_call: $("#next_to_call").val()
                , next_call: $("#next_call").val()
                , call_results_id: $("#call_results_id").val()
                , calls_id
            }
            , function(result) {
                if (result.error != null) {
                    alert(result.error);
                } else {
                    @if(isset($students_id) && $students_id != null)
                    GoBackWithRefresh();
                    return false;
                    @else
                    $('#successfullCall').css('display', 'block');
                    $('#successfullCall').text('با موفقیت ثبت شد.');
                    $('#call_results_id').val(null);
                    $('#description').val(null);
                    $("#products_id").val([]).trigger('change');
                    $("#notices_id").val("").trigger('change');
                    theSearch();
                    @endif
                }
            }).fail(function() {
            alert('خطای بروز رسانی');
        });
    }

    function StudentCollection() {
        emptySomeData();
        var has_collection = $("#has_collection").val().trim();
        if (has_collection == '' || has_collection == 'false') {
            has_collection = 'true';
        } else {
            has_collection = 'false';
        }
        $("#has_collection").val(has_collection);
        if (has_collection == 'false')
            $("#student-collection-btn").removeClass('btn-success').addClass('btn-warning');
        else
            $("#student-collection-btn").removeClass('btn-warning').addClass('btn-success');
        table.ajax.reload();
        return false;
    }

    function StudentTag() {
        emptySomeData();
        var has_tag = $("#has_tag").val().trim();
        if (has_tag == '' || has_tag == 'false') {
            has_tag = 'true';
        } else {
            has_tag = 'false';
        }
        $("#has_tag").val(has_tag);
        table.ajax.reload();
        return false;
    }

    function StudentReminder() {
        emptySomeData();
        var has_reminder = $("#has_reminder").val().trim();
        if (has_reminder == '' || has_reminder == 'false') {
            has_reminder = 'true';
        } else {
            has_reminder = 'false';
        }
        $("#has_reminder").val(has_reminder);
        if (has_reminder == 'false')
            $("#student-reminder-btn").removeClass('btn-success').addClass('btn-warning');
        else
            $("#student-reminder-btn").removeClass('btn-warning').addClass('btn-success');
        table.ajax.reload();
        return false;
    }

    function StudentSite() {
        emptySomeData();
        var has_site = $("#has_site").val().trim();
        if (has_site == '' || has_site == 'false') {
            has_site = 'true';
        } else {
            has_site = 'false';
        }
        $("#has_site").val(has_site);
        if (has_site == 'false')
            $("#student-site-btn").removeClass('btn-success').addClass('btn-warning');
        else
            $("#student-site-btn").removeClass('btn-warning').addClass('btn-success');
        table.ajax.reload();
        return false;
    }

    function OrderCollection() {
        emptySomeData();
        var order_collection = $("#order_collection").val().trim();
        if (order_collection == '' || order_collection == 'false') {
            order_collection = 'true';
        } else {
            order_collection = 'false';
        }
        $("#order_collection").val(order_collection);
        if (order_collection == 'false')
            $("#order-collection-btn").removeClass('btn-success').addClass('btn-warning');
        else
            $("#order-collection-btn").removeClass('btn-warning').addClass('btn-success');
        table.ajax.reload();
        return false;
    }

    function selectProduct() {
        $('#loading').css('display', 'inline');
        emptySomeData();
        $("#has_the_product").val($("#has_product").val().join(','));
        table.ajax.reload();
    }

    function selectTag() {
        $("#has_the_tags").val($("#has_the_tag").val().join(','));
        table.ajax.reload();
        //console.log($('#has_the_tags').val());
    }
    function selectNeedTag() {
        $("#has_need_tags").val($("#has_need_tag").val().join(','));
        table.ajax.reload();
        console.log($('#has_need_tags').val());
    }
    function selectCallResult() {
        $('#loading').css('display', 'inline');
        emptySomeData();
        $("#has_call_result").val($("#has_cal_result").val());
        table.ajax.reload();
    }

    function selectMajors() {
        $('#loading').css('display', 'inline');
        emptySomeData();
        $('#major').val($('#major').val());
        table.ajax.reload();
    }


    function selectSources() {
        $('#loading').css('display', 'inline');
        emptySomeData();
        $('#sources_id').val($('#sources_id').val());
        table.ajax.reload();
    }

    function selectEducationLevels() {
        $('#loading').css('display', 'inline');
        emptySomeData();
        $('#eduction_level').val($('#education_level').val());
        table.ajax.reload();
    }

    function theSearch() {
        $('#loading').css('display', 'inline');
        emptySomeData();
        lastPage = table.page();
        isSearchCall = true;
        table.ajax.reload();
        return false;
    }


    $(function() {
        $('input').keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                $('#loading').css('display', 'inline');
                emptySomeData();
                table.ajax.reload();
            }
        });


        $('#next_call_persian').MdPersianDateTimePicker({
            targetTextSelector: '#next_call_persian'
            , targetDateSelector: '#next_call'
        , });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".btn-danger").click(function(e) {
            if (!confirm('آیا مطمئنید؟')) {
                e.preventDefault();
            }
        });
        $('select.select2').select2();
        table = $("#example2").DataTable({
            "paging": true
            , "lengthChange": false
            , "searching": false
            , "ordering": true
            , "info": true
            , "autoWidth": false
            , "language": {
                "paginate": {
                    "previous": "قبل"
                    , "next": "بعد"
                }
                , "emptyTable": "داده ای برای نمایش وجود ندارد"
                , "info": "نمایش _START_ تا _END_ از _TOTAL_ داده"
                , "infoEmpty": "نمایش 0 تا 0 از 0 داده"
                , "proccessing": "در حال بروزرسانی"
            }
            , "columnDefs": [ ////define columns
                {
                    "searchable": false
                    , "orderable": false
                    , "targets": 0
                }
                , {
                    "searchable": false
                    , "orderable": false
                    , "targets": 7
                }
                , {
                    "searchable": false
                    , "orderable": false
                    , "targets": 8
                }
            , ]
            , "order": [
                    [1, 'asc']
                ] /// sort columns 2
            , serverSide: true
            , processing: true
            , ajax: {
                "type": "POST"
                , "url": "{{ route('student_level_3', ['id' => $user ? $user->id : Auth::user()->id, 'level' => null]) . ((isset($students_id) && $students_id!=null)?'?students_id=' . $students_id . '&calls_id=' . $calls_id:'') }}"
                , "dataType": "json"
                , "contentType": 'application/json; charset=utf-8',

                "data": function(data) {
                    data['sources_id'] = $("#sources_id").val();
                    data['main_id'] = theMain;
                    data['auxilary_id'] = theAuxilary;
                    data['second_auxilary_id'] = theSecondAuxilary;
                    data['third_auxilary_id'] = theThirdAuxilary;
                    data['name'] = $("#name").val();
                    data['phone'] = $("#phone").val();
                    data['has_collection'] = $("#has_collection").val();
                    data['has_the_product'] = $("#has_the_product").val();
                    data['has_the_tags'] = $("#has_the_tags").val();
                    data['has_need_tags'] = $('#has_need_tags').val();
                    data['has_call_result'] = $("#has_call_result").val();
                    data['has_site'] = $("#has_site").val();
                    data['order_collection'] = $("#order_collection").val();
                    data['has_reminder'] = $("#has_reminder").val();
                    data['has_tag'] = $("#has_tag").val();
                    data['education_level'] = $("#education_level").val();
                    data['major'] = $('#major').val();
                    data['conditions'] = $('#conditions').val();
                    data['current_page'] = lastPage;
                    return JSON.stringify(data);
                }
                , "complete": function(response) {
                    $('#loading').css('display', 'none');
                    $('#example2_paginate').removeClass('dataTables_paginate');
                    students = JSON.parse(response.responseText).students;
                    if(isSearchCall) {
                        table.page(lastPage).draw( 'page' );
                        isSearchCall = false;
                    }
                    $('#example2 tr').click(function() {
                        var tr = this;
                        if (!$(this).hasClass('table_header')) {
                            var studentId = parseInt($(tr).find('td')[1].innerText, 10);
                            if (!isNaN(studentId)) {
                                for (var index in students) {
                                    if (students[index].id == studentId) {
                                        showMorePanel(index, tr);
                                    }
                                }
                            }
                        }
                    });
                    @if(isset($students_id) && $students_id != null)
                    var tr = $('#example2 tr')[1];
                    if (!$(this).hasClass('table_header')) {

                        var studentId = parseInt($(tr).find('td')[1].innerText, 10);
                        if (!isNaN(studentId)) {
                            for (var index in students) {
                                if (students[index].id == studentId) {
                                    showMorePanel(index, tr);
                                    $('#call_modal').modal('show');
                                }
                            }
                        }
                    }
                    @endif
                }

            }
            , columns: [{
                    data: null
                }
                , {
                    data: 'id'
                }
                , {
                    data: 'first_name'
                }
                , {
                    data: 'last_name'
                }
                , {
                    data: 'users_id'
                }
                , {
                    data: 'sources_id'
                }
                , {
                    data: 'tags'
                }
                , {
                    data: 'temps'
                }
                , {
                    data: 'end'
                }
            ]
        , });
        table.on('draw.dt', function() {
            var info = table.page.info();
            table.column(0, {
                search: 'applied'
                , order: 'applied'
                , page: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1 + info.start;
            });
        });
        const theTable = table;
    });

</script>
@endsection
