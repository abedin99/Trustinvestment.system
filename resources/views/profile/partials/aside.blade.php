<div class="col-md-3">

    <!-- Profile Image -->
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="text-center">
                <img class="profile-user-img img-fluid img-circle" src="{{ asset('dist/img/avatar5.png') }}"
                    alt="User profile picture">
            </div>

            <h3 class="profile-username text-center">{{ $user->name }}</h3>

            <p class="text-muted text-center">{{ $user->gender }}</p>

        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <!-- About Me Box -->
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">About Me</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

            <p class="text-muted">{{ $user->address }}</p>

            <hr>

            <strong><i class="fas fa-book mr-1"></i> Profession</strong>

            <p class="text-muted">
                {{ $user->profession }}
            </p>

            <hr>

            <strong><i class="fas fa-phone-alt mr-1"></i> Contact</strong>

            <p class="text-muted">
                <span class="tag tag-danger">{{ $user->phone }}</span>
                <br>
                <span class="tag tag-success">{{ $user->email }}</span>
            </p>

            <hr>

            <strong><i class="far fa-file-alt mr-1"></i> Others</strong>

            <p class="text-muted">
                <span class="tag tag-danger">Age: {{ $user->age }}</span>
                <br>
                <span class="tag tag-success">Weight: {{ $user->weight }}</span>
            </p>

        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>