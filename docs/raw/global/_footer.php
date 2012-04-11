
    <div id="footer">
        {ce:build:extract file=build_path."../../config.php" vars="PROFORM_NAME|PROFORM_VERSION"}
            <p>{PROFORM_NAME} version {PROFORM_VERSION}</p>
        {/ce:build:extract}
    </div>

    </div>
</div>

</body>
</html>