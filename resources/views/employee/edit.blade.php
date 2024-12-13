<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    @vite('resources/sass/app.scss')
</head>
<body>
    @extends('layouts.app')

@section('content')

    <body style="background-color: #f8f9fa;">
        <div class="container-sm my-5">
            <div class="row justify-content-center">
                <div class="col-xl-6">
                    <div class="p-4"
                        style="background: #ffffff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                        <form action="{{ route('employees.update', ['employee' => $employee->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="text-center mb-4">
                                <i class="bi-person-circle fs-1"></i>
                                <h4 style="font-weight: bold; color: #333;">Edit Employee</h4>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input class="form-control @error('firstName') is-invalid @enderror" type="text"
                                        name="firstName" id="firstName"
                                        value="{{ $errors->any() ? old('firstName') : $employee->firstname }}"
                                        placeholder="Enter First Name">
                                    @error('firstName')
                                        <div class="text-danger"><small>{{ $message }}</small></div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input class="form-control @error('lastName') is-invalid @enderror" type="text"
                                        name="lastName" id="lastName"
                                        value="{{ $errors->any() ? old('lastName') : $employee->lastname }}"
                                        placeholder="Enter Last Name">
                                    @error('lastName')
                                        <div class="text-danger"><small>{{ $message }}</small></div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="text"
                                        name="email" id="email"
                                        value="{{ $errors->any() ? old('email') : $employee->email }}"
                                        placeholder="Enter Email">
                                    @error('email')
                                        <div class="text-danger"><small>{{ $message }}</small></div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="age" class="form-label">Age</label>
                                    <input class="form-control @error('age') is-invalid @enderror" type="number"
                                        name="age" id="age"
                                        value="{{ $errors->any() ? old('age') : $employee->age }}" placeholder="Enter Age">
                                    @error('age')
                                        <div class="text-danger"><small>{{ $message }}</small></div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <select name="position" id="position" class="form-select">
                                        @php
                                            $selected = '';
                                            if ($errors->any()) {
                                                $selected = old('position');
                                            } else {
                                                $selected = $employee->position_id;
                                            }
                                        @endphp
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}"
                                                {{ $selected == $position->id ? 'selected' : '' }}>
                                                {{ $position->code . ' - ' . $position->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position')
                                        <div class="text-danger"><small>{{ $message }}</small></div>
                                    @enderror
                                </div>
                                <div class="mt-4">
                                    @if ($employee->original_filename)
                                        <label for="cv" class="form-label">Curriculum Vitae (CV)</label>
                                        <h6>{{ $employee->original_filename }}</h6>
                                        <a href="{{ route('employees.download', $employee->id) }}"
                                            class="btn btn-primary btn-sm mt-2">
                                            Download CV
                                        </a>
                                        <br></br>
                                    @endif
                                </div>
                                <!-- Input File CV -->
                                <input type="file" class="form-control" id="cv" name="cv"
                                    accept=".pdf,.doc,.docx">

                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6 d-grid">
                                    <a href="{{ route('employees.index') }}" class="btn btn-outline-dark btn-lg mt-3"><i
                                            class="bi-arrow-left-circle me-2"></i> Cancel</a>
                                </div>
                                <div class="col-md-6 d-grid">
                                    <button type="submit" class="btn btn-dark btn-lg mt-3"><i
                                            class="bi-check-circle me-2"></i> Edit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>

@endsection
@vite('resources/js/app.js')

</body>
</html>
