<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);

$allowed_origin = 'https://andrew02.it';
$secret = "Uq?M}_jarMS(NH!%ofKFk[/Ro['I>h";
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}
require_once __DIR__ . '/../src/functions.php';
include_once './conn.php';

router('GET', '^/$', function(){
    echo 'API Connected';
});
router('POST', '^/auth', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['email'], $data['pw']) &&  strlen($data['email']) <= 150 && strlen($data['pw']) > 7 && strlen($data['pw']) <= 50) {
        $sql = 'SELECT `u`.*, `c`.`expiring_discount`, `c`.`discount` FROM `users` `u` LEFT JOIN `customer_card` `c` ON `c`.`id_cliente` = `u`.`id` WHERE `u`.`email` = \''.$db->real_escape_string($data['email']).'\'';
        $ds = $db->query($sql);
        $_U = $ds->fetch_assoc();
        if (!empty($_U['id']) && dekrypt($_U['password']) == $data['pw']) {
            $payload = [
                'id' => $_U['id'],
                'email' => $_U['email'],
                'name' => $_U['name'],
                'surname' => $_U['surname'],
                'discount' => $_U['discount'],
                'expiring_discount' => $_U['expiring_discount'],
                'iat' => time()
            ];
            echo json_encode([
                'result' => 1,
                'token' => createJWT($payload, $secret),
                'data' => $payload
            ]);
            return;
        } else {
            echo json_encode(['result' => 0, 'msg' => 'Wrong Email or Password']);
            return;
        }
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/admin-login', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['email'], $data['pw']) &&  strlen($data['email']) <= 150 && strlen($data['pw']) > 7 && strlen($data['pw']) <= 50) {
        $sql = 'SELECT `u`.* FROM `users` `u` WHERE `u`.`email` = \''.$db->real_escape_string($data['email']).'\' AND `u`.`is_admin` = 1';
        //die(var_dump($sql));
        $ds = $db->query($sql);
        $_U = $ds->fetch_assoc();
        if (!empty($_U['id']) && dekrypt($_U['password']) == $data['pw']) {
            $payload = [
                'id' => $_U['id'],
                'email' => $_U['email'],
                'name' => $_U['name'],
                'surname' => $_U['surname'],
                'shop_id' => $_U['shop_id'],
                'iat' => time()
            ];
            echo json_encode([
                'result' => 1,
                'token' => createJWT($payload, $secret),
                'data' => $payload
            ]);
            return;
        } else {
            echo json_encode(['result' => 0, 'msg' => 'Wrong Email or Password']);
            return;
        }
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/register', function() use ($db) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (
        isset($data['email'], $data['pw'], $data['name'], $data['surname']) &&
        strlen($data['email']) < 150 &&
        strlen($data['pw']) < 50 &&
        strlen($data['name']) < 50 &&
        strlen($data['surname']) < 50) {
        $sql = 'SELECT `id` FROM `users` WHERE `email` = \'' . $db->real_escape_string($data['email']) . '\'';
        $ds = $db->query($sql);
        $_U = $ds->fetch_assoc();
        if (!empty($_U['id'])) {
            echo json_encode(['result' => 0, 'msg' => 'Email already taken']);
            return;
        } else {
            $sql = 'INSERT INTO `users` (`email`, `password`, `name`, `surname`) VALUES (\''.$db->real_escape_string($data['email']).'\', \''.krypt($data['pw']).'\', \''.$db->real_escape_string($data['name']).'\', \''.$db->real_escape_string($data['surname']).'\')';
            $db->query($sql);
            echo json_encode([
                'result' => 1
            ]);
            return;
        }
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('GET', '^/getFilters', function() use ($db) {
    header('Content-Type: application/json');
    $html = '<ul class="filtri">
                <li class="accordion">
                    <p class="filter-title">Brand</p>
                    <ul id="brand" class="minimize hidden">';
    $sql = 'SELECT * FROM `brand`';
    $ds = $db->query($sql);
    while ($rs = $ds->fetch_assoc()) {
        $html .= '<li class="option"><input type="checkbox" name="brand[]" id="brand_'.$rs['id'].'" value="'.$rs['id'].'" class="hidden"><label for="brand_'.$rs['id'].'">'.$rs['value'].'</label></li>';
    }
    $html .= '
                    </ul>
                </li>
                <li class="accordion">
                    <p class="filter-title">Genere</p>
                    <ul id="gender" class="minimize hidden">';
    $sql = 'SELECT * FROM `gender`';
    $ds = $db->query($sql);
    while ($rs = $ds->fetch_assoc()) {
        $html .= '<li class="option"><input type="checkbox" name="gender[]" id="gender_'.$rs['id'].'" value="'.$rs['id'].'" class="hidden"><label for="gender_'.$rs['id'].'">'.$rs['value'].'</label></li>';
    }
    $html .= '
                    </ul>
                </li>
                <li class="accordion">
                    <p class="filter-title">Flex</p>
                    <ul id="flex" class="minimize hidden">';
    $sql = 'SELECT * FROM `flex`';
    $ds = $db->query($sql);
    while ($rs = $ds->fetch_assoc()) {
        $html .= '<li class="option"><input type="checkbox" name="flex[]" id="flex_'.$rs['id'].'" value="'.$rs['id'].'" class="hidden"><label for="flex_'.$rs['id'].'">'.$rs['value'].'</label></li>';
    }
    $html .= '
                    </ul>
                </li>
                <li class="accordion">
                    <p class="filter-title">Style of riding</p>
                    <ul id="style" class="minimize hidden">';
    $sql = 'SELECT * FROM `style_of_riding`';
    $ds = $db->query($sql);
    while ($rs = $ds->fetch_assoc()) {
        $html .= '<li class="option"><input type="checkbox" name="style_of_riding[]" value="'.$rs['id'].'" id="style_of_riding_'.$rs['id'].'" class="hidden"><label for="style_of_riding_'.$rs['id'].'">'.$rs['value'].'</label></li>';
    }
    $html .= '
                    </ul>
                </li>
                <li class="accordion">
                    <p class="filter-title">Shape</p>
                    <ul id="shape" class="minimize hidden">';
    $sql = 'SELECT * FROM `shape`';
    $ds = $db->query($sql);
    while ($rs = $ds->fetch_assoc()) {
        $html .= '<li class="option"><input type="checkbox" name="shape[]" id="shape_'.$rs['id'].'" value="'.$rs['id'].'" class="hidden"><label for="shape_'.$rs['id'].'">'.$rs['value'].'</label></li>';
    }
    $html .= '
                    </ul>
                </li>
                <li class="accordion">
                    <p class="filter-title">Peso</p>
                    <input class="minimize hidden" type="number" name="weight" id="weight" min="0" max="200">
                </li>
                <li class="accordion">
                    <p class="filter-title">Taglia</p>
                    <input class="minimize hidden" type="number" name="size" id="size" min="0" max="200">
                </li>
            </ul>
            <button id="reset">RESET</button><button id="search">PERSONALIZZA</button>';
    echo json_encode(['result' => 1, 'html' => $html]);
});
router('POST', '^/getModels', function() use ($db) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = 'SELECT `m`.`id`, `m`.`name`, `m`.`image`, `m`.`price` FROM `model` `m` INNER JOIN `type_length` `t` ON `m`.`id_type_lenghts` = `t`.`id`';
    $filtering = false;
    if(!empty($data['filtri'])){
        if(!empty($data['filtri']['brand'])){
            $sql .= ($filtering ? ' AND' : ' WHERE').' `m`.`id_brand` IN('.implode(',', $data['filtri']['brand']).')';
            $filtering = true;
        }
        if(!empty($data['filtri']['gender'])){
            $sql .= ($filtering ? ' AND' : ' WHERE').' `m`.`id_gender` IN('.implode(',', $data['filtri']['gender']).')';
            $filtering = true;
        }
        if(!empty($data['filtri']['style'])){
            $sql .= ($filtering ? ' AND' : ' WHERE').' `m`.`id_style_of_riding` IN('.implode(',', $data['filtri']['style']).')';
            $filtering = true;
        }
        if(!empty($data['filtri']['shape'])){
            $sql .= ($filtering ? ' AND' : ' WHERE').' `m`.`id_shape` IN('.implode(',', $data['filtri']['shape']).')';
            $filtering = true;
        }
        if(!empty($data['filtri']['flex'])){
            $sql .= ($filtering ? ' AND' : ' WHERE').' `m`.`id_flex` IN('.implode(',', $data['filtri']['flex']).')';
            $filtering = true;
        }
        if($data['filtri']['weight'] > 0){
            $sql .= ($filtering ? ' AND' : ' WHERE').' `m`.`weight_min` < '.$data['filtri']['weight'].' AND `m`.`weight_max` > '.$data['filtri']['weight'];
            $filtering = true;
        }
        if($data['filtri']['size'] > 0){
            $sql .= ($filtering ? ' AND' : ' WHERE').' `t`.`min` < '.$data['filtri']['size'].' AND `t`.`max` > '.$data['filtri']['size'];
            $filtering = true;
        }
    }
    $ds = $db->query($sql);
    $models = [];
    while ($rs = $ds->fetch_assoc()) {
        $models[] = $rs;
    }
    echo json_encode(['result' => 1, 'models' => $models]);
});
router('GET', '^/getGenders', function() use ($db) {
    header('Content-Type: application/json');
    $sql = 'SELECT * FROM `gender` ORDER BY `id`';
    $ds = $db->query($sql);
    $genders = [];
    while ($rs = $ds->fetch_assoc()) {
        $genders[] = $rs;
    }
    echo json_encode(['result' => 1, 'data' => $genders]);
});
router('POST', '^/saveGender', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'], $data['value'])) {
        $sql = 'UPDATE `gender` SET `value` = \''.$db->real_escape_string($data['value']).'\' WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/deleteGender', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $sql = 'DELETE FROM `gender` WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/createGender', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['value'])) {
        $sql = 'INSERT INTO `gender` SET `value` = \''.$db->real_escape_string($data['value']).'\'';
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('GET', '^/getBrands', function() use ($db) {
    header('Content-Type: application/json');
    $sql = 'SELECT * FROM `brand` ORDER BY `id`';
    $ds = $db->query($sql);
    $brands = [];
    while ($rs = $ds->fetch_assoc()) {
        $brands[] = $rs;
    }
    echo json_encode(['result' => 1, 'data' => $brands]);
});
router('POST', '^/saveBrand', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'], $data['value'])) {
        $sql = 'UPDATE `brand` SET `value` = \''.$db->real_escape_string($data['value']).'\' WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/deleteBrand', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $sql = 'DELETE FROM `brand` WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/createBrand', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['value'])) {
        $sql = 'INSERT INTO `brand` SET `value` = \''.$db->real_escape_string($data['value']).'\'';
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('GET', '^/getFlexs', function() use ($db) {
    header('Content-Type: application/json');
    $sql = 'SELECT * FROM `flex` ORDER BY `id`';
    $ds = $db->query($sql);
    $flexs = [];
    while ($rs = $ds->fetch_assoc()) {
        $flexs[] = $rs;
    }
    echo json_encode(['result' => 1, 'data' => $flexs]);
});
router('POST', '^/saveFlex', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'], $data['value'])) {
        $sql = 'UPDATE `flex` SET `value` = \''.$db->real_escape_string($data['value']).'\' WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/deleteFlex', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $sql = 'DELETE FROM `flex` WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/createFlex', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['value'])) {
        $sql = 'INSERT INTO `flex` SET `value` = \''.$db->real_escape_string($data['value']).'\'';
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('GET', '^/getStyles', function() use ($db) {
    header('Content-Type: application/json');
    $sql = 'SELECT * FROM `style_of_riding` ORDER BY `id`';
    $ds = $db->query($sql);
    $styles = [];
    while ($rs = $ds->fetch_assoc()) {
        $styles[] = $rs;
    }
    echo json_encode(['result' => 1, 'data' => $styles]);
});
router('POST', '^/saveStyle', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'], $data['value'])) {
        $sql = 'UPDATE `style_of_riding` SET `value` = \''.$db->real_escape_string($data['value']).'\' WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/deleteStyle', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $sql = 'DELETE FROM `style_of_riding` WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/createStyle', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['value'])) {
        $sql = 'INSERT INTO `style_of_riding` SET `value` = \''.$db->real_escape_string($data['value']).'\'';
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('GET', '^/getShapes', function() use ($db) {
    header('Content-Type: application/json');
    $sql = 'SELECT * FROM `shape` ORDER BY `id`';
    $ds = $db->query($sql);
    $shapes = [];
    while ($rs = $ds->fetch_assoc()) {
        $shapes[] = $rs;
    }
    echo json_encode(['result' => 1, 'data' => $shapes]);
});
router('POST', '^/saveShape', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'], $data['value'])) {
        $sql = 'UPDATE `shape` SET `value` = \''.$db->real_escape_string($data['value']).'\' WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/deleteShape', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $sql = 'DELETE FROM `shape` WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/createShape', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['value'])) {
        $sql = 'INSERT INTO `shape` SET `value` = \''.$db->real_escape_string($data['value']).'\'';
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('GET', '^/getSnows', function() use ($db) {
    header('Content-Type: application/json');
    $shop_id = null;
    if(isset($_GET['uid'])) {
        $sql  = '
            SELECT `shop_id` FROM `users` WHERE `id` = '.intval($_GET['uid']);
        $ds = $db->query($sql);
        $shop_id = $ds->fetch_assoc()['shop_id'];
    }
    $sql = '
        SELECT * FROM `model` '.($shop_id ? 'WHERE `shop_id`='.$shop_id : '').' ORDER BY `id`
    ';
    $ds = $db->query($sql);
    $snows = [];
    while ($rs = $ds->fetch_assoc()) {
        $snows[] = $rs;
    }
    $sql = 'SELECT * FROM `gender` ORDER BY `id`';
    $ds = $db->query($sql);
    $genders = [];
    while ($rs = $ds->fetch_assoc()) {
        $genders[] = $rs;
    }
    $sql = 'SELECT * FROM `brand` ORDER BY `id`';
    $ds = $db->query($sql);
    $brands = [];
    while ($rs = $ds->fetch_assoc()) {
        $brands[] = $rs;
    }
    $sql = 'SELECT * FROM `flex` ORDER BY `id`';
    $ds = $db->query($sql);
    $flexs = [];
    while ($rs = $ds->fetch_assoc()) {
        $flexs[] = $rs;
    }
    $sql = 'SELECT * FROM `style_of_riding` ORDER BY `id`';
    $ds = $db->query($sql);
    $styles = [];
    while ($rs = $ds->fetch_assoc()) {
        $styles[] = $rs;
    }
    $sql = 'SELECT * FROM `shape` ORDER BY `id`';
    $ds = $db->query($sql);
    $shapes = [];
    while ($rs = $ds->fetch_assoc()) {
        $shapes[] = $rs;
    }
    $sql = 'SELECT * FROM `type_length` ORDER BY `id`';
    $ds = $db->query($sql);
    $sizes = [];
    while ($rs = $ds->fetch_assoc()) {
        $sizes[] = $rs;
    }
    echo json_encode(['result' => 1, 'data' => $snows, 'sizes' => $sizes, 'genders' => $genders, 'brands' => $brands, 'flexs' => $flexs, 'styles' => $styles, 'shapes' => $shapes]);
});
router('POST', '^/saveSnow', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'], $data['data'])) {
        $sql = '
                UPDATE `model` 
                SET 
                    `id_brand` = '.intval($data['data']['brand']).',
                    `id_type_lenghts` = '.intval($data['data']['size']).', 
                    `name` = \''.$db->real_escape_string($data['data']['name']).'\',
                    `image` = \'default.webp\',
                    `description` = \''.$db->real_escape_string($data['data']['description']).'\', 
                    `id_style_of_riding` = '.intval($data['data']['style']).', 
                    `id_gender` = '.intval($data['data']['gender']).', 
                    `id_shape` = '.intval($data['data']['shape']).', 
                    `id_flex` = '.intval($data['data']['flex']).', 
                    `weight_min` = '.intval($data['data']['w_min']).', 
                    `weight_max` = '.intval($data['data']['w_max']).', 
                    `size` = '.intval($data['data']['length']).', 
                    `price` = '.floatval($data['data']['price']).'
                WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/deleteSnow', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $sql = 'DELETE FROM `model` WHERE `id` = '.intval($data['id']);
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/createSnow', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $shop_id = null;
    if(isset($data['uid'])) {
        $sql  = '
            SELECT `shop_id` FROM `users` WHERE `id` = '.intval($data['uid']);
        $ds = $db->query($sql);
        $shop_id = $ds->fetch_assoc()['shop_id'];
    }
    if ($data['data']) {
        $sql = '
                INSERT `model` 
                SET 
                    `id_brand` = '.intval($data['data']['brand']).',
                    `id_type_lenghts` = '.intval($data['data']['size']).', 
                    `name` = \''.$db->real_escape_string($data['data']['name']).'\',
                    `image` = \'default.webp\',
                    `description` = \''.$db->real_escape_string($data['data']['description']).'\', 
                    `id_style_of_riding` = '.intval($data['data']['style']).', 
                    `id_gender` = '.intval($data['data']['gender']).', 
                    `id_shape` = '.intval($data['data']['shape']).', 
                    `id_flex` = '.intval($data['data']['flex']).', 
                    `weight_min` = '.intval($data['data']['w_min']).', 
                    `weight_max` = '.intval($data['data']['w_max']).', 
                    `size` = '.intval($data['data']['length']).', 
                    `price` = '.floatval($data['data']['price']).($shop_id ? ', `shop_id` = '.intval($shop_id) : '');
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Invalid data, check your form and try again']);
});
router('POST', '^/getCart', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['cart'])) {
        $sql  = '
            SELECT `id`, `name`, `price` FROM `model` WHERE `id` IN(\''.implode('\',\'', array_column($data['cart'], 'id')).'\')';
        $ds = $db->query($sql);
    }
    $html = '
        <ul class="cart">
    ';
    while ($rs = $ds->fetch_assoc()) {
        $index = array_search(intval($rs['id']), array_column($data['cart'], 'id'));
        $html .= '
            <li>'.$rs['name'].' x'.$data['cart'][$index]['qta'].' - '.number_format( floatval($rs['price'])*intval($data['cart'][$index]['qta']), 2, ',', '').'€</li>
        ';
    }
    if(!$ds->num_rows) {
        $html .= 'Nessun articolo aggiunto al carrello.';
    }
    $html .= '</ul>';
    echo json_encode(['result' => 1, 'html' => $html]);
});
router('GET', '^/getOrders', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $sql = '
        SELECT `id`, `data_consegna`, `indirizzo` FROM `order` WHERE NOT `is_export`
    ';
    $ds = $db->query($sql);
    $orders = [];
    while ($rs = $ds->fetch_assoc()) {
        $orders[] = $rs;
    }
    $sql = 'SELECT `id`, `indirizzo` FROM `shop`';
    $ds = $db->query($sql);
    $negozi = '<select class="negozio">';
    while ($rs = $ds->fetch_assoc()) {
        $negozi .= '<option value="'.$rs['id'].'">'.$rs['indirizzo'].'</option>';
    }
    $negozi .= '</select>';
    echo json_encode(['result' => 1, 'orders' => $orders, 'negozi' => $negozi]);
});
router('POST', '^/order', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['cart']) && !empty($data['cart'])) {
        $sql = '
            SELECT * FROM `customer_card` WHERE `id_cliente` = '.intval($data['uid']);
        $ds = $db->query($sql);
        $customer_card = false;
        if($ds->num_rows) {
            $customer_card = $ds->fetch_assoc();

        }

        $sql = '
            INSERT INTO `order`
            SET
                `id_cliente` = '.intval($data['uid']).',
                `indirizzo` = \'Via Fittizia 99\',
                `sconto` = '.($customer_card ? $customer_card['discount'] : 0).',
                `data_pagamento` = \''.date('Y-m-d').'\',
                `data_consegna` = \''.date('Y-m-d', strtotime('+ 10 days')).'\'
        ';
        $db->query($sql);
        $order_id = $db->insert_id;
        $sql  = '
            SELECT `id`, `price` FROM `model` WHERE `id` IN(\''.implode('\',\'', array_column($data['cart'], 'id')).'\')';
        $ds = $db->query($sql);
        $total_price = 0;
        while ($rs = $ds->fetch_assoc()) {
            $index = array_search(intval($rs['id']), array_column($data['cart'], 'id'));
            $sql = '
                INSERT INTO `order_detail` SET 
                        `order_id` = '.intval($order_id).',
                        `prezzo_storico` = '.$rs['price'].',
                        `qta` = '.intval($data['cart'][$index]['qta']).',
                        `snowboard_id` = '.intval($rs['id']);
            $db->query($sql);
            $total_price += $rs['price'] * $data['cart'][$index]['qta'];
        }
        $discount = max($total_price - ($customer_card ? $customer_card['discount'] : 0), 0) / 10;
        $sql = '
            REPLACE INTO `customer_card`
            SET
                `id_cliente` = '.intval($data['uid']).',
                `expiring_discount` = DATE_ADD(NOW(), INTERVAL 1 YEAR),
                `discount` = '.number_format($discount, 2, '.', '');
        $db->query($sql);
        echo json_encode(['result' => 1]);
        return;
    }
    echo json_encode(['result' => 0, 'msg' => 'Nessun articolo selezionato']);
});
router('POST', '^/export', function() use ($db, $secret) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['id']) && isset($data['negozio'])) {
        $payload = json_encode(['order_id' => intval($data['id']), 'negozio' => intval($data['negozio'])]);
        $ch = curl_init('https://andrew02.it/export.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($response, true);
        if (isset($responseData['url'])) {
            $sql = 'UPDATE `order` SET `is_export` = 1 WHERE `id` = '.intval($data['id']);
            $db->query($sql);

            $pdfUrl = $responseData['url'];
            $sql = '
            INSERT INTO `work_order` SET `order_id` = '.intval($data['id']).', `shop_id` = '.intval($data['negozio']).', `url` = \''.$db->real_escape_string($pdfUrl).'\'';
            $db->query($sql);
            echo json_encode(['result' => 1]);
            return;
        } else {
            die(var_dump($responseData));
        }
    }
    echo json_encode(['result' => 0, 'msg' => 'Nessun articolo selezionato']);
});
router('GET', '^/getWorks', function() use ($db) {
    header('Content-Type: application/json');
    $sql  = '
        SELECT 
            `w`.`id`,
            `o`.`data_consegna` `data`,
            CONCAT(`u`.`name`, \' \',`u`.`surname`) `cliente`,
            `s`.`indirizzo` `negozio`,
            `w`.`url`
        FROM `work_order` `w`
        INNER JOIN `order` `o` ON `w`.`order_id` = `o`.`id`
        INNER JOIN `users` `u` ON `u`.`id` = `o`.`id_cliente`
        INNER JOIN `shop` `s` ON `s`.`id` = `w`.`shop_id`
        ';
    if(isset($_GET['sid']) && ($_GET['sid'] > 0)) {
        $sql .= '
            WHERE `w`.`shop_id` = '.intval($_GET['sid']);
    }
    $ds = $db->query($sql);
    $works = [];
    while ($rs = $ds->fetch_assoc()) {
        $works[] = $rs;
    }
    echo json_encode(['result' => 1, 'orders' => $works]);
});


function krypt($string){
    return  base64_encode($string);
}
function dekrypt($input){
    return base64_decode($input);
}
function createJWT($payload, $secret) {
    $header = base64_encode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
    $payload['exp'] = time() + (60 * 60 * 4);
    $payload = base64_encode(json_encode($payload));
    $signature = base64_encode(hash_hmac("sha256", "$header.$payload", $secret, true));
    return "$header.$payload.$signature";
}
function verifyJWT($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    list($header, $payload, $signature) = $parts;
    $validSignature = base64_encode(hash_hmac("sha256", "$header.$payload", $secret, true));
    if ($signature !== $validSignature) return false;
    $payloadDecoded = json_decode(base64_decode($payload), true);
    return ($payloadDecoded["exp"] >= time()) ? $payloadDecoded : false;
}
header("HTTP/1.0 404 Not Found");
echo '404 Not Found';
