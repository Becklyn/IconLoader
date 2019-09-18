1.x to 2.0
==========

*   Icons are now automatically HTML wrapped:

    *   before: `{{ icon("test") }} = '<svg ...>'`
    *   now: `{{ icon("app/test") }} = '<span class="icon icon-test"><svg ...></span>'`

*   Namespaces are now mandatory, to update add a namespace:

    *   before:
        ```yaml
        becklyn_icon_loader:
            search_glob: "build/mayd/*/icon"
        ```
        and use `{{ icon("test") }}`
        
    *   now:
        ```yaml
        becklyn_icon_loader:
            namespaces:
                app: "build/mayd/*/icon"
        ```
        and use `{{ icon("app/test") }}`
