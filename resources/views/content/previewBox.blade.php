<div id="content" class="Content-container Content-container--fill">

    <div class="Content-toolbar ButtonTabGroup">
        <button type="button" class="Content-refresh Button-color--black Button--smallPadding">
            <span class="Icon Icon-refresh Icon--light"></span>
            <span class="Text--hidden">Refresh</span>
        </button>

        <button type="button" class="Content-collapseToggle Button-color--black Button--smallPadding"
                data-enabled="true">
            <span class="Toggle--ifDisabled">
                <span class="Icon Icon-expand Icon--light"></span>
                <span class="Text--hidden">Expand</span>
            </span>
            <span class="Toggle--ifEnabled">
                <span class="Icon Icon-times Icon--light"></span>
                <span class="Text--hidden">Exit</span>
            </span>
        </button>
    </div>

    <iframe src="{{ URL::route($blueprint->getRouteName('getContent'), $item->getId()) }}"
            class="Content-preview"></iframe>

</div>

<?php Event::listen('oxygen.layout.page.after', function() { ?>

<script>
    (function () {
        window.Oxygen = window.Oxygen || {};
        window.Oxygen.load = window.Oxygen.load || [];

        window.Oxygen.load.push(function () {
            var content = document.getElementById("content");

            document.querySelector(".Content-refresh").addEventListener("click", function () {
                document.querySelector(".Content-preview").contentWindow.location.reload();
            });

            var toggle = new Toggle(
                    document.querySelector(".Content-collapseToggle"),
                    function () {
                        content.classList.add("Content-container--fill");
                        window.Oxygen.setBodyScrollable(false);
                    },
                    function () {
                        content.classList.remove("Content-container--fill");
                        window.Oxygen.setBodyScrollable(true);
                    }
            );

            window.Oxygen.setBodyScrollable(false);
        });
    })();

</script>

<?php }); ?>
