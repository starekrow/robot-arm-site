<pic-thumb>
    <a href='#' onclick={clicked}>
        <img ref=image src='api/thumb/{opts.name}'></img>
    </a>
    <style>
        :scope {
            display: inline-block;
            margin: 5px;
        }
        img {
            max-width: 95px;
            max-height: 95px;
            border: 1px solid #aaa;
        }
        img.active {
            border: 1px solid #f44;
        }
    </style>
    <script>
        clicked() {
            console.log(this.opts.name);
            mainScreen.showImage(this.opts.name);
            if (mainScreen.activeThumb) {
                mainScreen.activeThumb.deactivate();
            }
            mainScreen.activeThumb = this;
            this.activate();
        }
        activate() {
            this.refs.image.classList.add("active");
        }
        deactivate() {
            this.refs.image.classList.remove("active");
        }
    </script>
</pic-thumb>