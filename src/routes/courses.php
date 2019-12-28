<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

$app = new App;

$app->get('/courses', function(Request $request, Response $response) {

   $db = new Db();

    try {
        $db = $db->connect();

        $courses = $db->query("select * from courses")->fetchAll(PDO::FETCH_OBJ);

         return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson($courses);

    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );
    }

});

$app->get('/course/{id}', function(Request $request, Response $response) {

    $id = $request->getAttribute("id");
    $db = new Db();

    try {
        $db = $db->connect();

        $course = $db->query("select * from courses where id = $id")->fetch(PDO::FETCH_OBJ);

        if ($course) {
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson($course);
        } else {
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text" => "Belirtilen kurs bulunamadı."
                    )
                ));
        }

    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );
    }

});

$app->post('/course/add', function(Request $request, Response $response) {

    $title = $request->getParam("title");
    $couponCode = $request->getParam("couponCode");
    $price = $request->getParam("price");

    $db = new Db();

    try {
        $db = $db->connect();
        $statement = "INSERT INTO courses (title, couponCode, price) VALUES(:title, :couponCode, :price)";
        $prepare = $db->prepare($statement);

        $prepare->bindParam("title", $title);
        $prepare->bindParam("couponCode", $couponCode);
        $prepare->bindParam("price", $price);
        $result = $prepare->execute();

        if ($result){
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(
                    array (
                        "text" => "Başarıyla eklendi."
                    )
                );
        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(
                    array(
                        "error" => array(
                            "text" => "Bir hata oluştu."
                        )
                )
            );
        }

    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );
    }

});

$app->put('/course/update/{id}', function(Request $request, Response $response) {

    $id = $request->getAttribute("id");

    if ($id) {
        $title = $request->getParam("title");
        $couponCode = $request->getParam("couponCode");
        $price = $request->getParam("price");

        $db = new Db();

        try {
            $db = $db->connect();
            $statement = "UPDATE courses SET title = :title, couponCode = :couponCode, price = :price WHERE id = $id";
            $prepare = $db->prepare($statement);

            $prepare->bindParam("title", $title);
            $prepare->bindParam("couponCode", $couponCode);
            $prepare->bindParam("price", $price);
            //$prepare->bindParam("id", $id);
            $result = $prepare->execute();

            if ($result){
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(
                        array (
                            "text" => "Başarıyla güncellenmiştir."
                        )
                    );
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(
                        array(
                            "error" => array(
                                "text" => "Güncelleme sırasında hata oluştu."
                            )
                        )
                    );
            }

        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "error" => array(
                        "text" => $e->getMessage(),
                        "code" => $e->getCode()
                    )
                )
            );
        }

    } else {
        return $response->withStatus(500)->withJson(
            array(
                "error" => array(
                    "text" => "Geçersiz id verisi."
                )
            )
        );
    }



});

$app->delete('/course/delete/{id}', function(Request $request, Response $response) {

    $id = $request->getAttribute("id");

    if ($id) {

        $db = new Db();

        try {
            $db = $db->connect();
            $statement = "DELETE FROM courses WHERE id = :id";
            $prepare = $db->prepare($statement);

            $prepare->bindParam("id", $id);
            $result = $prepare->execute();

            if ($result){
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(
                        array (
                            "text" => "Başarıyla silinmiştir."
                        )
                    );
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(
                        array(
                            "error" => array(
                                "text" => "Silme sırasında hata oluştu."
                            )
                        )
                    );
            }

        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "error" => array(
                        "text" => $e->getMessage(),
                        "code" => $e->getCode()
                    )
                )
            );
        }

    } else {
        return $response->withStatus(500)->withJson(
            array(
                "error" => array(
                    "text" => "Geçersiz id verisi."
                )
            )
        );
    }

});