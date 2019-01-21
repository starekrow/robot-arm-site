<pic-thumb>
    <a href='#' onclick={clicked}>
        <img src='/api/thumb/{opts.name}'></img>
    </a>
    <style>
        :scope {
            display: inline-block;
            margin: 5px;
        }
        img {
            max-width: 50px;
            max-height: 50px;
        }
    </style>
    <script>
        clicked() {
            console.log(this.opts.name);
            mainScreen.showImage(this.opts.name);
        }
    </script>
</pic-thumb>