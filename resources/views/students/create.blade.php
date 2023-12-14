@extends('layouts.index')

@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>دانش آموز</h1>
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
                    <form method="POST" enctype="multipart/form-data" id="crtStudent">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="first_name">نام</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="نام" value="{{ $student->first_name }}" />
                                    @else
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="نام" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="last_year_grade">تراز یا رتبه سال قبل</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="last_year_grade" name="last_year_grade" placeholder="تراز/رتبه" value="{{ $student->last_year_grade }}" />
                                    @else
                                    <input type="number" class="form-control" id="last_year_grade" name="last_year_grade" placeholder="تراز/رتبه" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="parents_job_title">شغل پدر یا مادر</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="text" class="form-control" id="parents_job_title" name="parents_job_title" placeholder="شغل" value="{{ $student->parents_job_title }}" />
                                    @else
                                    <input type="text" class="form-control" id="parents_job_title" name="parents_job_title" placeholder="شغل" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="egucation_level">مقطع</label>
                                    <select id="egucation_level" name="egucation_level" class="form-control">
                                        <option value="">
                                        <option>
                                            @if (isset($student) && isset($student->id) && $student->egucation_level == "6")
                                        <option value="6" selected>
                                            @else
                                        <option value="6">
                                            @endif
                                            6
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "7")
                                        <option value="7" selected>
                                            @else
                                        <option value="7">
                                            @endif
                                            7
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "8")
                                        <option value="8" selected>
                                            @else
                                        <option value="8">
                                            @endif
                                            8
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "9")
                                        <option value="9" selected>
                                            @else
                                        <option value="9">
                                            @endif
                                            9
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "10")
                                        <option value="10" selected>
                                            @else
                                        <option value="10">
                                            @endif
                                            10
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "11")
                                        <option value="11" selected>
                                            @else
                                        <option value="11">
                                            @endif
                                            11
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "12")
                                        <option value="12" selected>
                                            @else
                                        <option value="12">
                                            @endif
                                            12
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "13")
                                        <option value="13" selected>
                                            @else
                                        <option value="13">
                                            @endif
                                            فارغ التحصیل
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->egucation_level == "14")
                                        <option value="14" selected>
                                            @else
                                        <option value="14">
                                            @endif
                                            دانشجو
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="average">معدل</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="text" class="form-control" id="average" name="average" placeholder="معدل" value="{{ $student->average }}" />
                                    @else
                                    <input type="text" class="form-control" id="average" name="average" placeholder="معدل" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="outside_consultants">مشاور بیرونی</label>
                                    @if (isset($student) && isset($student->outside_consultants))
                                    <input type="text" class="form-control" id="outside_consultants" name="outside_consultants" placeholder="مشاور بیرونی" value="{{ $student->outside_consultants }}" />
                                    @else
                                    <input type="text" class="form-control" id="outside_consultants" name="outside_consultants" placeholder="مشاور بیرونی" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="introducing">معرف</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="text" class="form-control" id="introducing" name="introducing" placeholder="معرف" value="{{ $student->introducing }}" />
                                    @else
                                    <input type="text" class="form-control" id="introducing" name="introducing" placeholder="معرف" />
                                    @endif
                                </div>
                                @if(Gate::allows('parameters'))
                                <div class="form-group">
                                    <label for="supporters_id">پشتیبان</label>
                                    <select id="supporters_id" name="supporters_id" class="form-control">
                                        <option value="0"></option>
                                        @foreach ($supports as $item)
                                        @if (isset($student) && isset($student->id) && $student->supporters_id == $item->id)
                                        <option value="{{ $item->id }}" selected>
                                            @else
                                        <option value="{{ $item->id }}">
                                            @endif
                                            {{ $item->first_name }} {{ $item->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                @if(!Gate::allows('supervisor') && Gate::allows('parameters'))
                                <div class="form-group">
                                    <label for="level">سطح</label>
                                    <select id="level" name="level" class="form-control">
                                        <option value="0"></option>
                                        @for($i = 1; $i <=4; $i++) @if (isset($student) && isset($student->id) && $student->level == $i)
                                            <option value="{{ $i }}" selected>
                                                @else
                                            <option value="{{ $i }}">
                                                @endif
                                                {{ $i }}
                                            </option>

                                            @endfor
                                    </select>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label for="school">مدرسه</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="text" class="form-control" id="school" name="school" placeholder="مدرسه" value="{{ $student->school }}" />
                                    @else
                                    <input type="text" class="form-control" id="school" name="school" placeholder="مدرسه" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="major">رشته</label>
                                    <select id="major" name="major" class="form-control">
                                        @if (isset($student) && isset($student->id) && $student->major == "mathematics")
                                        <option value="mathematics" selected>
                                            @else
                                        <option value="mathematics">
                                            @endif
                                            ریاضی
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->major == "experimental")
                                        <option value="experimental" selected>
                                            @else
                                        <option value="experimental">
                                            @endif
                                            تجربی
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->major == "humanities")
                                        <option value="humanities" selected>
                                            @else
                                        <option value="humanities">
                                            @endif
                                            انسانی
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->major == "art")
                                        <option value="art" selected>
                                            @else
                                        <option value="art">
                                            @endif
                                            هنر
                                        </option>
                                        @if (isset($student) && isset($student->id) && $student->major == "other")
                                        <option value="other" selected>
                                            @else
                                        <option value="other">
                                            @endif
                                            دیگر
                                        </option>
                                    </select>
                                </div>


                                @if((!Gate::allows('supervisor') && Gate::allows('parameters'))||(Gate::allows('supervisor')))
                                <div class="form-group">
                                    <label for="sources_id">منبع</label>
                                    <select id="sources_id" name="sources_id" class="form-control"  >
                                        <option value="0"></option>
                                        @foreach ($sources as $item)
                                        @if (isset($student) && isset($student->id) && $student->sources_id == $item->id)
                                        <option value="{{ $item->id }}" selected>
                                            @else
                                        <option value="{{ $item->id }}">
                                            @endif
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>                               
                                @endif

                                <div class="form-group">
                                    <label for="cities_id">شهر</label>
                                    <select id="cities_id" name="cities_id" class="form-control">
                                        <option value="0"></option>
                                        @foreach ($cities as $item)
                                        @if (isset($student) && isset($student->cities_id) && $student->cities_id == $item->id)
                                        <option value="{{ $item->id }}" selected>
                                            @else
                                        <option value="{{ $item->id }}">
                                            @endif
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="national_no">کد ملی </label>
                                    @if (isset($student) && isset($student->nationality_code))
                                    <input type="number" class="form-control" id="national_no" name="national_no" placeholder="کد ملی" value="{{ $student->nationality_code }}" />
                                    @else
                                    <input type="number" class="form-control" id="national_no" name="national_no" placeholder="کد ملی" />
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="concours_year">سال کنکور </label>
                                    @if (isset($student) && isset($student->concours_year))
                                    <input type="number" class="form-control" id="concours_year" name="concours_year" placeholder="سال کنکور" value="{{ $student->concours_year }}" />
                                    @else
                                    <input type="number" class="form-control" id="concours_year" name="concours_year" placeholder="سال کنکور" />
                                    @endif
                                </div>


                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="last_name">نام خانوادگی <span style="color: red;">*</span></label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="نام خانوادگی" value="{{ $student->last_name }}" required />
                                    @else
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="نام خانوادگی" required />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="consultants_id">مشاور</label>
                                    <select id="consultants_id" name="consultants_id" class="form-control">
                                        <option value="0"></option>
                                        @foreach ($consultants as $item)
                                        @if (isset($student) && isset($student->id) && $student->consultants_id == $item->id)
                                        <option value="{{ $item->id }}" selected>
                                            @else
                                        <option value="{{ $item->id }}">
                                            @endif
                                            {{ $item->first_name }} {{ $item->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="home_phone">تلفن منزل</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="home_phone" name="home_phone" placeholder="تلفن منزل" value="{{ $student->home_phone }}" />
                                    @else
                                    <input type="number" class="form-control" id="home_phone" name="home_phone" placeholder="تلفن منزل" />
                                    @endif
                                </div>



                                <div class="form-group">
                                    <label for="phone">تلفن <span style="color: red;">*</span></label>
                                    @if (isset($student) && isset($student->id))
                                    <input required type="number" class="form-control" id="phone" name="phone" placeholder="تلفن" value="{{ $student->phone }}" />
                                    @else
                                    <input required type="number" class="form-control" id="phone" name="phone" placeholder="تلفن" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="phone1"> شماره تلفن ۱ </label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="phone1" name="phone1" placeholder="تلفن ۱" value="{{ $student->phone1 }}" />
                                    @else
                                    <input type="number" class="form-control" id="phone1" name="phone1" placeholder="تلفن ۱" />
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="phone2"> شماره تلفن ۲ </label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="phone2" name="phone2" placeholder="تلفن ۲" value="{{ $student->phone2 }}" />
                                    @else
                                    <input type="number" class="form-control" id="phone2" name="phone2" placeholder="تلفن ۲" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="phone3"> شماره تلفن ۳ </label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="phone3" name="phone3" placeholder="تلفن ۳" value="{{ $student->phone3 }}" />
                                    @else
                                    <input type="number" class="form-control" id="phone3" name="phone3" placeholder="تلفن ۳" />
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="phone4"> شماره تلفن ۴ </label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="phone4" name="phone4" placeholder=" تلفن ۴" value="{{ $student->phone4 }}" />
                                    @else
                                    <input type="number" class="form-control" id="phone4" name="phone4" placeholder=" تلفن ۴" />
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="student_phone">تلفن دانش آموز</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="student_phone" name="student_phone" placeholder="تلفن" value="{{ $student->student_phone }}" />
                                    @else
                                    <input type="number" class="form-control" id="student_phone" name="student_phone" placeholder="تلفن" />
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="mother_phone">تلفن مادر</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="mother_phone" name="mother_phone" placeholder="تلفن مادر" value="{{ $student->mother_phone }}" />
                                    @else
                                    <input type="number" class="form-control" id="mother_phone" name="mother_phone" placeholder="تلفن مادر" />
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="father_phone">تلفن پدر</label>
                                    @if (isset($student) && isset($student->id))
                                    <input type="number" class="form-control" id="father_phone" name="father_phone" placeholder="تلفن پدر" value="{{ $student->father_phone }}" />
                                    @else
                                    <input type="number" class="form-control" id="father_phone" name="father_phone" placeholder="تلفن پدر" />
                                    @endif
                                </div>


                                <div class="form-group">
                                    <label for="description">توضیحات</label>
                                    @if (isset($student) && isset($student->description))
                                    <input type="text" class="form-control" id="description" name="description_exists" placeholder="توضیحات" value="{{ $student->description }}" {{ (in_array(auth()->user()->group->type,['support','marketer','consultant'])) ? "readonly" : '' }} "  />
                            @else
                            <input type=" text" class="form-control" id="description" name="description" placeholder="توضیحات" />
                                    @endif
                                </div>

                                @if (isset($student) && isset($student->id))
                                <div class="form-group">
                                    <label for="banned">لیست سیاه</label><br />
                                    <input type="checkbox" id="banned" name="banned" @if($student->banned)
                                    checked
                                    @endif
                                    />
                                </div>
                                @endif
                                @if (isset($student) && isset($student->id))
                                <div class="form-group">
                                    <label for="archived">آرشیو</label><br />
                                    <input type="checkbox" id="archived" name="archived" @if($student->archived)
                                    checked
                                    @endif
                                    />
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-primary" onclick="return sendForm();">
                                    ذخیره
                                </button>
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
<!-- /.content -->
@endsection

@section('js')
<!-- Select2 -->
<script src="/plugins/select2/js/select2.full.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.select2').select2();
    });

    // function sendForm() {
    //     const phones = [];

    //     //let pattern = new RegExp('^([09]+[0-9]{9}|)$'); //pattern or null 
    //     let pattern = new RegExp('^(09(0[0-9]|1[0-9]|2[0-9]|3[0-9]|9[0-9])-?[0-9]{3}-?[0-9]{4}|)$'); //pattern or null 
    //     phones.push(pattern.test($("#phone").val()) ? $("#phone").val() : "");
    //     phones.push(pattern.test($("#phone1").val()) ? $("#phone1").val() : "");
    //     phones.push(pattern.test($("#phone2").val()) ? $("#phone2").val() : "");
    //     phones.push(pattern.test($("#phone3").val()) ? $("#phone3").val() : "");
    //     phones.push(pattern.test($("#phone4").val()) ? $("#phone4").val() : "");
    //     phones.push(pattern.test($("#student_phone").val()) ? $("#student_phone").val() : "");
    //     phones.push(pattern.test($("#mother_phone").val()) ? $("#mother_phone").val() : "");
    //     phones.push(pattern.test($("#father_phone").val()) ? $("#father_phone").val() : "");

    //     return checkForDuplicate(pattern, phones);

    // }

    // function checkForDuplicate(pattern, phones) {
    //     phonesInFunc = phones;
    //     //console.log(phonesInFunc);
    //     const msg = {
    //         0: "فرمت تلفن اشتباه وارد شده است.",
    //         1: "فرمت تلفن ۱ اشتباه وارد شده است.",
    //         2: "فرمت تلفن ۲ اشتباه وارد شده است.",
    //         3: "فرمت تلفن ۳ اشتباه وارد شده است.",
    //         4: "فرمت تلفن ۴ اشتباه وارد شده است.",
    //         5: "فرمت تلفن دانش آموز اشتباه وارد شده است.",
    //         6: "فرمت تلفن مادر دانش آموز اشتباه وارد شده است.",
    //         7: "فرمت تلفن پدر دانش آموز اشتباه وارد شده است.",

    //     }
    //     const msgduble = {
    //         0: "تلفن تکراری است",
    //         1: "تلفن ۱ تکراری است",
    //         2: "تلفن ۲ تکراری است",
    //         3: "تلفن ۳ تکراری است",
    //         4: "تلفن ۴ تکراری است",
    //         5: "تلفن دانش آموز تکراری است",
    //         6: "تلفن مادر دانش آموز تکراری است",
    //         7: "تلفن پدر دانش آموز تکراری است",
    //     }
    //     const variableName = {
    //         0: "phone",
    //         1: "phone1",
    //         2: "phone2",
    //         3: "phone3",
    //         4: "phone4",
    //         5: "student_phone",
    //         6: "mother_phone",
    //         7: "father_phone",

    //     }
    //     //console.log(phones);
    //     for (i = 0; i < 8; i++) {
    //         phone_tmp = phones[i];
    //         canChange = false;
    //         phones[i] = "";

    //         if (($.inArray(phone_tmp, phones) !== -1) && (phone_tmp != "")) {

    //             alert(msgduble[i]);
    //             //console.log(i);
    //             phones[i] = phone_tmp;
    //             canChange = true;
    //             return false;
    //         }
    //         if (!canChange) {
    //             phones[i] = phone_tmp;
    //             canChange = true;
    //         }
    //         if (!pattern.test($("#" + variableName[i]).val())) {
    //             alert(msg[i]);
    //             return false;
    //         }
    //     }
    //     return true;

    // }

    const sendForm = () => {
        const phones = [];
        const warnings = [];
        $(".phone-group").each((i, elm) => {
            phones.push(pattern.test(elm.value) ? elm.value : '');
            if (!pattern.test(elm.value)) warnings.push(`فرمت تلفن${names[i]} اشتباه وارد شده است.`);
        });
        if (warnings.length > 0) {
            alert(warnings.join('\n'));
            return false;
        }

        return checkDuplication(phones);

    };

    const names = ['', '۱', '۲', '۳', '۴', 'دانش آموز', 'مادر دانش آموز', 'پدر دانش آموز'];

    const toFindDuplicates = arry => arry.filter((item, index) => arry.indexOf(item) !== index);
    const checkDuplication = (phones) => {
        const duplicates = (toFindDuplicates(phones)).filter((phone) => phone !== '');
        if (duplicates.length > 0) {
            alert([...new Set(duplicates.map((phone) => `تلفن${names[phones.indexOf(phone)]} تکراری است`))].join(','));
            return false;
        }
        return true;
    };

    

</script>
@endsection