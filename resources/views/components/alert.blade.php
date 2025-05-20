<div id="alert-container">

    @if (session('success'))
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div id="warning-alert" class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
        </div>
    @endif
    @if (session('info'))
        <div id="info-alert" class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
        </div>
    @endif
    @if (session('error'))
        <div id="danger-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div id="danger-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul style="list-style-type: none;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
