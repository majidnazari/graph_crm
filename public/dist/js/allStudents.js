var index = 0
let lastPage = 1
let isSearchCall = false
let tags = {}
let stu_id = 0
let collections = {}
let sw = 0;
let theColumns = [
    { data: null },
    { data: 'id' },
    { data: 'first_name' },
    { data: 'last_name' },
    { data: 'users_id' },
    { data: 'sources_id' },
    { data: 'tags' },
    { data: 'temps' },
    { data: 'supporters_id' },
    { data: 'description' },
    { data: 'end' }
]
let theColumnDefs = [
    {
        searchable: false,
        orderable: false,
        targets: 0
    },
    {
        searchable: false,
        orderable: false,
        targets: 10
    },
    {
        searchable: false,
        orderable: false,
        targets: 7
    }
]
if(theRoute == "student_all"){
    theColumns = [
        { data: null },
        { data: 'id' },
        { data: 'first_name' },
        { data: 'last_name' },
        { data: 'users_id' },
        { data: 'sources_id' },
        { data: 'tags' },
        { data: 'temps' },
        { data: 'supporters_id' },
        { data: 'level'},
        { data: 'description' },
        { data: 'end' }
    ]
    theColumnDefs = [
        {
            searchable: false,
            orderable: false,
            targets: 0
        },
        {
            searchable: false,
            orderable: false,
            targets: 11
        },
        {
            searchable: false,
            orderable: false,
            targets: 7
        }
    ]
}
var table
for (let tg of tmpTags) {
    tags[tg.id] = tg
}
for (let cl of tmpCollections) {
    collections[cl.id] = cl
}
let filterParents = {
    parent1: '',
    parent2: '',
    parent3: '',
    parent4: '',
    need_parent1: '',
    need_parent2: '',
    need_parent3: '',
    need_parent4: ''
}
function showMorePanel (index, tr) {
    var editRoute = route_edit;
    var purchaseRoute = route_purchase;
    var test = `<table style="width: 100%">
            <tr>
                <td>
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                تراز یا رتبه سال قبل :
                                ${
                                    students[index].last_year_grade != null
                                        ? students[index].last_year_grade
                                        : ''
                                }
                            </div>
                            <div class="col">
                                مشاور :
                                ${
                                    students[index].consultant
                                        ? students[index].consultant
                                              .first_name +
                                          ' ' +
                                          students[index].consultant.last_name
                                        : ''
                                }
                            </div>
                            <div class="col">
                                شغل پدر یا مادر :
                                ${
                                    students[index].parents_job_title != null
                                        ? students[index].parents_job_title
                                        : ''
                                }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                شماره منزل :
                                ${
                                    students[index].home_phone != null
                                        ? students[index].home_phone
                                        : ''
                                }
                            </div>
                            <div class="col">
                                مقطع :
                                ${
                                    students[index].egucation_level != null
                                        ? egucation_levels[
                                              students[index].egucation_level
                                          ]
                                            ? egucation_levels[
                                                  students[index]
                                                      .egucation_level
                                              ]
                                            : students[index].egucation_level
                                        : ''
                                }
                            </div>
                            <div class="col">
                                شماره موبایل والدین :
                                ${
                                    students[index].father_phone != null
                                        ? students[index].father_phone
                                        : ''
                                }
                                ${
                                    students[index].mother_phone != null
                                        ? students[index].mother_phone
                                        : ''
                                }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                مدرسه :
                                ${
                                    students[index].school != null
                                        ? students[index].school
                                        : ''
                                }
                            </div>
                            <div class="col">
                                معدل :
                                ${
                                    students[index].average != null
                                        ? students[index].average
                                        : ''
                                }
                            </div>
                            <div class="col">
                                رشته تحصیلی :
                                ${
                                    majors[students[index].major]
                                        ? majors[students[index].major]
                                        : '-'
                                }
                            </div>

                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="${editRoute.replace(
                                    '-1',
                                    students[index].id
                                )}">
                                    ویرایش مشخصات
                                </a>
                            </div>
                            <div class="col">
                                تاریخ ثبت دانش آموز :
                                ${students[index].pcreated_at}
                            </div>
                            <div class="col">
                                تلفن دانش آموز:
                               ${students[index].phone}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="#" onclick="$('#students_index').val(${index});preloadTagModal('moral');$('#tag_modal').modal('show'); return false;">
                                    برچسب روحیات اخلاقی
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="#" onclick="$('#students_index').val(${index});preloadTagModal('need');$('#tag_modal').modal('show'); return false;">
                                    برچسب نیازهای دانش آموز
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a target="_blank" href="${purchaseRoute.replace(
                                    '-1',
                                    students[index].id
                                )}">
                                    گزارش خریدهای قطعی دانش آموز
                                </a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>`

    // var tr = $("#tr-" + index)[0];
    var row = table.row(tr)
    if (row.child.isShown()) {
        row.child.hide()
    } else {
        row.child(test).show()
    }
}
    function changeLevel (studentsIndex, id) {
    if (students[studentsIndex]) {
        var students_id = id;
        $('#loading-' + studentsIndex).show();
        $.post(levelRoute,
            {
                "students_id":students_id,
                "level":$('#level_'+ studentsIndex).val()
            },
            function (result) {
                $('#loading-' + studentsIndex).hide()
                if (result && result.error != null) {
                    alert(result.error)
                }
            }
        ).fail(function () {
            $('#loading-' + studentsIndex).hide()
            alert('خطای بروز رسانی')
            table.ajax.reload()
        })
    }
    return false
    }
function changeSupporter (studentsIndex, id) {
    if (students[studentsIndex]) {
        var students_id = id
        var supporters_id = $('#supporters_id_' + studentsIndex).val()
        $('#loading-' + studentsIndex).show()
        $.post(route_student_supporter,
            {
                students_id,
                supporters_id
            },
            function (result) {
                $('#loading-' + studentsIndex).hide()
                if (result && result.error != null) {
                    alert(result.error)
                }
            }
        ).fail(function () {
            $('#loading-' + studentsIndex).hide()
            console.log(result)
            alert('خطای بروز رسانی')
            table.ajax.reload()
        })
    }
    return false
}
function selectParentOne (dobj) {
    filterParents.parent1 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterTagsByParent()
}
function selectParentTwo (dobj) {
    filterParents.parent2 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterTagsByParent()
}
function selectParentThree (dobj) {
    filterParents.parent3 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterTagsByParent()
}
function selectParentFour (dobj) {
    filterParents.parent4 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterTagsByParent()
}
function filterTagsByParent () {
    $('input.tag-checkbox').show()
    $('span.tag-title').show()
    $('br.tag-br').show()
    $('input.tag-checkbox').each(function (id, field) {
        let tagId = parseInt($(field).val(), 10)
        let theTag = tags[tagId]
        if (theTag) {
            if (filterParents.parent1 != '') {
                if (filterParents.parent1 != theTag.parent1) {
                    $(field).hide()
                    $('#tag-title-' + tagId).hide()
                    $('#tag-br-' + tagId).hide()
                }
            }
            if (filterParents.parent2 != '') {
                if (filterParents.parent2 != theTag.parent2) {
                    $(field).hide()
                    $('#tag-title-' + tagId).hide()
                    $('#tag-br-' + tagId).hide()
                }
            }
            if (filterParents.parent3 != '') {
                if (filterParents.parent3 != theTag.parent3) {
                    $(field).hide()
                    $('#tag-title-' + tagId).hide()
                    $('#tag-br-' + tagId).hide()
                }
            }
            if (filterParents.parent4 != '') {
                if (filterParents.parent4 != theTag.parent4) {
                    $(field).hide()
                    $('#tag-title-' + tagId).hide()
                    $('#tag-br-' + tagId).hide()
                }
            }
        }
    })
}
function selectNeedParentOne (dobj) {
    filterParents.need_parent1 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterNeedTagsByParent()
}
function selectNeedParentTwo (dobj) {
    filterParents.need_parent2 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterNeedTagsByParent()
}
function selectNeedParentThree (dobj) {
    filterParents.need_parent3 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterNeedTagsByParent()
}
function selectNeedParentFour (dobj) {
    filterParents.need_parent4 =
        $(dobj).val() != '' ? parseInt($(dobj).val(), 10) : ''
    filterNeedTagsByParent()
}
function filterNeedTagsByParent () {
    $('input.needtag-checkbox').show()
    $('span.needtag-title').show()
    $('br.needtag-br').show()
    $('input.needtag-checkbox').each(function (id, field) {
        // console.log('checking', field)
        let tagId = parseInt($(field).val(), 10)
        let theTag = collections[tagId]
        //console.log(tagId, theTag)
        if (theTag) {
            if (filterParents.need_parent1 != '') {
                if (filterParents.need_parent1 != theTag.need_parent1) {
                    $(field).hide()
                    $('#needtag-title-' + tagId).hide()
                    $('#needtag-br-' + tagId).hide()
                }
            }
            if (filterParents.need_parent2 != '') {
                if (filterParents.need_parent2 != theTag.need_parent2) {
                    $(field).hide()
                    $('#needtag-title-' + tagId).hide()
                    $('#needtag-br-' + tagId).hide()
                }
            }
            if (filterParents.need_parent3 != '') {
                if (filterParents.need_parent3 != theTag.need_parent3) {
                    $(field).hide()
                    $('#needtag-title-' + tagId).hide()
                    $('#needtag-br-' + tagId).hide()
                }
            }
            if (filterParents.need_parent4 != '') {
                if (filterParents.need_parent4 != theTag.need_parent4) {
                    $(field).hide()
                    $('#needtag-title-' + tagId).hide()
                    $('#needtag-br-' + tagId).hide()
                }
            }
        }
    })
}
function selectCollectionOne (dobj) {
    $('#collection-two')
        .find('option')
        .show()
    $('#collection-two')
        .find('option[value=""]')
        .prop('selected', true)
    if ($(dobj).val() != '') {
        $('#collection-two')
            .find('option')
            .each(function (id, field) {
                if (
                    $(field).data('parent_id') != $(dobj).val() &&
                    $(field).val() != ''
                ) {
                    $(field).hide()
                } else {
                    $(field).show()
                }
            })
    }
    $('#collection-three')
        .find('option')
        .show()
    $('#collection-three')
        .find('option[value=""]')
        .prop('selected', true)
    if ($(dobj).val() != '') {
        $('#collection-three')
            .find('option')
            .each(function (id, field) {
                if (
                    $(field).data('parent_parent_id') != $(dobj).val() &&
                    $(field).val() != ''
                ) {
                    $(field).hide()
                } else {
                    $(field).show()
                }
            })
    }
    filterCollectionsByParent()
}
function selectCollectionTwo (dobj) {
    // console.log('hey');
    $('#collection-three')
        .find('option')
        .show()
    $('#collection-three')
        .find('option[value=""]')
        .prop('selected', true)
    if ($(dobj).val() != '') {
        $('#collection-three')
            .find('option')
            .each(function (id, field) {
                if ($(field).data('parent_id') != $(dobj).val()) {
                    $(field).hide()
                } else {
                    $(field).show()
                }
            })
    }
    filterCollectionsByParent()
}
function selectCollectionThree (dobj) {
    filterCollectionsByParent()
}
function filterCollectionsByParent () {
    $('input.collection-checkbox').show()
    $('span.collection-title').show()
    $('br.collection-br').show()
    let collectionParents = $('#collection-two').val()
    let parents = []
    if ($('#collection-one').val() == '' && collectionParents == '') {
        return false
    }

    if (collectionParents == '') {
        parents.push(parseInt($('#collection-one').val(), 10))
        $('#collection-two')
            .find('option')
            .each(function (id, field) {
                if (
                    $(field).css('display') != 'none' &&
                    !isNaN(parseInt($(field).val(), 10))
                ) {
                    parents.push(parseInt($(field).val(), 10))
                }
            })
        console.log('p1', parents)
        $('input.collection-checkbox').each(function (id, field) {
            let collectionId = parseInt($(field).val(), 10)
            if (parents.indexOf(collectionId) < 0) {
                $(field).hide()
                $('#collection-title-' + collectionId).hide()
                $('#collection-br-' + collectionId).hide()
            }
        })
        return false
    } else {
        parents.push(parseInt(collectionParents, 10))
    }

    if ($('#collection-three').val() == '') {
        parents.push(parseInt($('#collection-two').val(), 10))
        $('#collection-three')
            .find('option')
            .each(function (id, field) {
                if (
                    $(field).css('display') != 'none' &&
                    !isNaN(parseInt($(field).val(), 10))
                ) {
                    parents.push(parseInt($(field).val(), 10))
                }
            })
        console.log('p2', parents)
        $('input.collection-checkbox').each(function (id, field) {
            let collectionId = parseInt($(field).val(), 10)
            if (parents.indexOf(collectionId) < 0) {
                $(field).hide()
                $('#collection-title-' + collectionId).hide()
                $('#collection-br-' + collectionId).hide()
            }
        })
        return false
    } else {
        parents.push(parseInt($('#collection-three').val(), 10))
    }

    console.log('parents:', parents)

    $('input.collection-checkbox').each(function (id, field) {
        let collectionId = parseInt($(field).val(), 10)
        let theCollection = collections[collectionId]
        if (theCollection) {
            console.log(
                parents.indexOf(theCollection.id),
                parents.indexOf(theCollection.parent_id)
            )
            if (
                parents.indexOf(theCollection.id) < 0 &&
                parents.indexOf(theCollection.parent_id) < 0
            ) {
                $(field).hide()
                $('#collection-title-' + collectionId).hide()
                $('#collection-br-' + collectionId).hide()
            }
        }
    })
}
function preloadTagModal (mode) {
    if (mode == 'need') {
        $('div.needs').show()
        $('div.morals').hide()
    } else {
        $('div.needs').hide()
        $('div.morals').show()
    }
    $('input.tag-checkbox').prop('checked', false)
    $('input.collection-checkbox').prop('checked', false)
    var studentsIndex = parseInt($('#students_index').val(), 10)
    console.log(studentsIndex, students[studentsIndex])
    if (!isNaN(studentsIndex)) {
        if (students[studentsIndex]) {
            console.log(students[studentsIndex].studenttags)
            for (studenttag of students[studentsIndex].studenttags) {
                $('#tag_' + studenttag.tags_id).prop('checked', true)
                $('#needtag_' + studenttag.tags_id).prop('checked', true)
            }
            console.log(students[studentsIndex].studentcollections)
            for (studentcollection of students[studentsIndex]
                .studentcollections) {
                $('#collection_' + studentcollection.collections_id).prop(
                    'checked',
                    true
                )
            }
        }
    }
}
function onClickTemperature (id) {
    stu_id = id
    preloadTemperatureModal(id)
    $('#temperature_modal').modal('show')
    return false
}
function findIndexOfTemperatures (id) {
    for (var i = 0; i < students.length; i++) {
        if (students[i].id == id) {
            index = i
        }
    }
    return index
}
function preloadTemperatureModal (id) {
    $('input.temperature-checkbox').prop('checked', false)
    index = findIndexOfTemperatures(id)
    if (!isNaN(index)) {
        if (students[index]) {
            for (studenttemperature of students[index].studenttemperatures) {
                $('#temperature_' + studenttemperature.temperatures_id).prop(
                    'checked',
                    true
                )
            }
        }
    }
}
function saveTags () {
    var selectedTags = []
    var selectedColllections = []
    $('input.tag-checkbox:checked').each(function (id, field) {
        selectedTags.push(parseInt(field.value, 10))
    })
    $('input.needtag-checkbox:checked').each(function (id, field) {
        selectedColllections.push(parseInt(field.value, 10))
    })
    var studentsIndex = parseInt($('#students_index').val(), 10)
    if (!isNaN(studentsIndex)) {
        if (students[studentsIndex]) {
            console.log('selected tags', selectedTags)
            console.log('selected collections', selectedColllections)
            $.post(
                route_student_tag,
                {
                    students_id: students[studentsIndex].id,
                    selectedTags,
                    selectedColllections
                },
                function (result) {
                    console.log('Result', result)
                    if (result.error != null) {
                        alert('خطای بروز رسانی')
                    } else {
                        window.location.reload()
                    }
                }
            ).fail(function () {
                alert('خطای بروز رسانی')
            })
        }
    }
}
function saveTemperatures () {
    var selectedTemperatures = []
    $('input.temperature-checkbox:checked').each(function (id, field) {
        selectedTemperatures.push(parseInt(field.value, 10))
    })
    index = findIndexOfTemperatures(stu_id)
    if (!isNaN(index)) {
        if (students[index]) {
            $.post(
                route_student_temperature,
                {
                    students_id: stu_id,
                    selectedTemperatures
                },
                function (result) {
                    if (result.error != null) {
                        alert('خطای بروز رسانی')
                    } else {
                        window.location.reload()
                    }
                }
            ).fail(function () {
                alert('خطای بروز رسانی')
            })
        }
    }
}
function isEmpty (obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            return false
        }
    }

    return JSON.stringify(obj) === JSON.stringify({})
}
function theSearch () {
    $('#loading').css('display', 'inline')
    table.ajax.reload()
    return false
}
function theChange () {
    $('#loading').css('display', 'inline')
    table.ajax.reload()
    return false
}
function handle (e) {
    if (e.keyCode === 13) {
        $('#loading').css('display', 'inline')
        e.preventDefault() // Ensure it is only this code that runs
        table.ajax.reload()
        return false
    }
}
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    $('.btn-danger').click(function (e) {
        if (!confirm('آیا مطمئنید؟')) {
            e.preventDefault()
        }
    })
    $('select.select2').select2()

    table = $('#example2').DataTable({
        paging: true,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true, 
        autoWidth: false,
        language: {
            paginate: {
                previous: 'قبل',
                next: 'بعد'
            },
            emptyTable: 'داده ای برای نمایش وجود ندارد',
            info: 'نمایش _START_ تا _END_ از _TOTAL_ داده',
            infoEmpty: 'نمایش 0 تا 0 از 0 داده',
            proccessing: 'در حال بروزرسانی'
        },
        stateSave: true,
        columnDefs: theColumnDefs,
        order: [[1, 'asc']], /// sort columns 2
        serverSide: true,
        processing: true,
        ajax: {
            type: 'POST',
            url: route,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            dataSrc: ( json ) => {
                console.log({json});
                students = json.theStudents;
                return json.data;
            },
            data: function (data) {
                data['supporters_id'] = $('#supporters_id').val()
                data['sources_id'] = $('#sources_id').val()
                data['cities_id'] = $('#cities_id').val()
                data['egucation_level'] = $('#egucation_level').val()
                data['major'] = $('#major').val()
                data['school'] = $('#school').val()
                data['name'] = $('#name').val()
                data['phone'] = $('#phone').val()
                data['level'] = $('#level').val();
                return JSON.stringify(data)
            },
            complete: function (response) {
                $('#example2_paginate').removeClass('dataTables_paginate')
                $('#loading').css('display', 'none')
                if (isSearchCall) {
                    console.log(lastPage)
                    table.page(lastPage).draw('page')
                    isSearchCall = false
                }
                $('#example2 tr').click(function () {
                    var x = this
                    if ($(this).hasClass('odd') || $(this).hasClass('even')) {
                        var studentId = parseInt(
                            $(this).find('td')[1].innerText,
                            10
                        )
                        if (!isNaN(studentId)) {
                            for (var index in students) {
                                if (students[index].id == studentId) {
                                    showMorePanel(index, this)
                                    break
                                }
                            }
                        }
                    }
                })
            }
        },
        columns: theColumns
    })
    table.on('draw.dt', function () {
        var info = table.page.info()
        table
            .column(0, { search: 'applied', order: 'applied', page: 'applied' })
            .nodes()
            .each(function (cell, i) {
                cell.innerHTML = i + 1 + info.start
            })
    })

    $('#input').keyup(e => {
        console.log(e)
    })
})
