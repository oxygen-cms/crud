@extends(app('oxygen.layout'))

@section('content')

    @include('oxygen/crud::versionable.itemHeader', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item, 'title' => $title])

    <div class="Block Block--noPadding">

        @include('oxygen/crud::content.previewBox')

    </div>

    @include('oxygen/crud::versionable.versions', ['item' => $item])

@stop