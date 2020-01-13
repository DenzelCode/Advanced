<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{@title} - {@name}</title>
</head>
<body>
    <p>There are two ways of accesing into a parameter:</p>
    <ul>
        <li><b>&#123;@name&#125;</b> It can be used just as an string to show up the parameter<br> <b>Example:</b> &#123;@name&#125; is the name of the website</li>
        <li><b>&#123;#= $name #&#125;</b> It can be used as a PHP variable, Everything inside of &#123;# #&#125; is PHP, it represents &#60;?php ?&#62; <br> <b>Example:</b> &#123;#= $name #&#125; is the name of the website <br> <b>Example:</b> &#123;# var_dump($name) #&#125; <br> <b>Example:</b> &#123;#= $authUser->getName() #&#125;</li>
    </ul>

    <p>Default parameters:</p>
    {#= "<pre>" . htmlentities(print_r($template->getDefaultParameters(), true)) . "</pre>" #}
    <a href="/">{#= $language->get("page.general.back") #}</a>
</body>
</html>