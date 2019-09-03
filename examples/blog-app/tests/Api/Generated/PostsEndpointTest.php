<?php

namespace Example\BlogApp\Test\Api\Generated;

use Example\BlogApp\TableGateways;
use Example\BlogApp\Models\PostsModel;
use Example\BlogApp\Services\PostsService;

class PostsEndpointTest extends \Gone\AppCore\Test\RoutesTestCase
{

    const MODEL_NAME = 'Posts';

    public function testPostsCreate()
    {
        $this->waypoint("Begin");
        /** @var PostsService $service */
        $service = $this->getApp()->getContainer()->get(PostsService::class);
        /** @var PostsModel $newPosts **/
        $newPosts = $service->getMockObject();

        $this->waypoint("Initialise Mock Model");
        $newPostsArray = $newPosts->__toArray();

        $method = "PUT";
        $uri    = "/v1/posts";

        $response = $this->request(
            $method,
            $uri,
            $newPostsArray
        );
        $this->waypoint("API PUT REST REQUEST");
        $body = (string) $response->getBody();

        ob_start();
        \Kint::dump(
            $newPostsArray,
            json_encode($newPostsArray, JSON_PRETTY_PRINT)
        );
        $requestPostsParams = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(
            in_array(
                $response->getStatusCode(),
                [
                    200,
                    400,
                ]
            ),
            "Response was not expected 200 or 400.\n" .
            "Request: {$method} => {$uri}" .
            "{$requestPostsParams}\n" .
            "Response body is: \n" .
            " ******************************\n{$body}\n ******************************\n"
        );

        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\" Request JSON blob: \"" . json_encode($newPosts->__toArray()) . "\""
        );
        $responseJson = json_decode($body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to PUT /v1/posts returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Posts', $responseJson);
        $this->waypoint("Some assertions");

        #\Kint::dump(
        #    $newPostsArray,
        #    $responseJson
        #);

        $this->validatePostsObject($responseJson['Posts']);
        $this->waypoint("Validate Object Response");
        //TODO: Make this respect primary key field instead of assuming ID.
        if(!isset($responseJson['Posts']['Id'])){
            $this->markTestIncomplete("Skipped test... Posts response object doesn't have an ID field.");
        }

        return $responseJson['Posts']['Id'];
    }


    public function testPostsCreateFails()
    {
        $this->waypoint("Begin");

        $newPosts = [
            'id' => null,
            'title' => null,
            'content' => null,
            'authorId' => null,
            'createdDate' => null,
            'publishedDate' => null,
            'deleted' => null,
        ];
        $response = $this->request("PUT", "/v1/posts", $newPosts);
        $this->waypoint("API PUT REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\" Request JSON blob: \"" . json_encode($newPosts) . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Fail", $responseJson['Status'], "Object was created, when failure was expected.");
        $this->waypoint("Some assertions");
    }

    /**
     * @depends testPostsCreate
     */
    public function testPostsGet($id)
    {
        $this->waypoint("Begin");
        $response = $this->request("GET", "/v1/posts/{$id}");
        $this->waypoint("API GET REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to GET /v1/posts/{$id} returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Posts', $responseJson);
        $this->waypoint("Some assertions");

        $this->validatePostsObject($responseJson['Posts']);
        $this->waypoint("Validate Object Response");
    }

    /**
     * @depends testPostsCreate
     */
    public function testPostsList()
    {
        $this->waypoint("Begin");
        $response = $this->request("GET", "/v1/posts");
        $this->waypoint("API REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to GET /v1/posts returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Posts', $responseJson);
        $this->waypoint("Some assertions");
        $this->validatePostsObject(reset($responseJson['Posts']));
        $this->waypoint("Validate Object Response");

    }

    /**
     * @depends testPostsCreate
     */
    public function testPostsDelete($id)
    {
        $response = $this->request("DELETE", "/v1/posts/{$id}");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to DELETE /v1/posts/{$id} returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertEquals("DELETE", $responseJson['Action']);
        $this->assertArrayHasKey('Posts', $responseJson);
        $this->validatePostsObject($responseJson['Posts']);
        return $id;
    }

    /**
     * @depends testPostsDelete
     */
    public function testPostsDeleteVerify($id)
    {
        $response = $this->request("GET", "/v1/posts/{$id}");
        $body = (string) $response->getBody();
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->assertEquals("Fail", $responseJson['Status']);
    }

    private function validatePostsObject($PostsObject)
    {
        $this->assertArrayHasKey('Id', $PostsObject, "There was no element with the key 'id'.");
        $this->assertArrayHasKey('Title', $PostsObject, "There was no element with the key 'title'.");
        $this->assertArrayHasKey('Content', $PostsObject, "There was no element with the key 'content'.");
        $this->assertArrayHasKey('AuthorId', $PostsObject, "There was no element with the key 'authorId'.");
        $this->assertArrayHasKey('CreatedDate', $PostsObject, "There was no element with the key 'createdDate'.");
        $this->assertArrayHasKey('PublishedDate', $PostsObject, "There was no element with the key 'publishedDate'.");
        $this->assertArrayHasKey('Deleted', $PostsObject, "There was no element with the key 'deleted'.");
    }
}
