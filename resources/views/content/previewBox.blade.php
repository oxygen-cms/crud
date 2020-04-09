<div id="content" class="Content-container Content-container--fill">

    <div class="Content-toolbar ButtonTabGroup ButtonTabGroup--dark">
        <button type="button" class="Content-refresh Button Button-color--black Button--smallPadding">
            <span class="fas fa-redo Icon--light"></span>
            <span class="Text--hidden">Refresh</span>
        </button>

        <button type="button" class="Content-collapseToggle Button Button-color--black Button--smallPadding"
                data-enabled="true">
            <span class="Toggle--ifDisabled">
                <span class="fas fa-expand-alt Icon--light"></span>
                <span class="Text--hidden">Expand</span>
            </span>
            <span class="Toggle--ifEnabled">
                <span class="fas fa-times Icon--light"></span>
                <span class="Text--hidden">Exit</span>
            </span>
        </button>
    </div>

    <iframe src="{{ URL::route($blueprint->getRouteName('getContent'), $item->getId()) }}"
            class="Content-preview"></iframe>

</div>
