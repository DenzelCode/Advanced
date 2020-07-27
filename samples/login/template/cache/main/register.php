<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{@title} - {@name}</title>
</head>
<body>
    <form action="/auth/register" method="POST">
        <input type="text" name="username" placeholder="<?=  $language->get("page.register.username") ?>">
        <input type="password" name="password" placeholder="<?=  $language->get("page.register.password") ?>">
        <input type="email" name="mail" placeholder="<?=  $language->get("page.register.mail") ?>">
        <label for="gender"><?=  $language->get("page.register.gender") ?></label>
        <select name="gender" id="gender">
            <option value="M"><?=  $language->get("page.register.genders.male") ?></option>
            <option value="F"><?=  $language->get("page.register.genders.female") ?></option>
        </select>
        <button type="submit" name="go"><?=  $language->get("page.register.register") ?></button>
    </form>
    <a href="/"><?=  $language->get("page.general.back") ?></a>
</body>
</html>