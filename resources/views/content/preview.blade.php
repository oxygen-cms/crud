@extends(app('oxygen.layout'))

@section('content')

    <?php
    $title = Lang::get('oxygen/crud::ui.namedResource.preview', [
            'name' => $item->getAttribute($crudFields->getTitleFieldName())
    ]);

    $sectionTitle = Lang::get('oxygen/crud::ui.resource.preview', [
            'resource' => $blueprint->getDisplayName()
    ]);
    ?>

    @include('oxygen/crud::versionable.itemHeader', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item, 'title' => $sectionTitle])

    <div class="Block Block--noPadding">

        @include('oxygen/crud::content.previewBox')

    </div>

    @include('oxygen/crud::versionable.versions', ['item' => $item, 'fields' => $crudFields])

@stop