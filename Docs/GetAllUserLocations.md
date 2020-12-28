**Get All Users**
----
  Returns json array of all `UserLocation` objects.

* **URL**

  /location/

* **Method:**

  `GET`

*  **URL Params**

  None

* **Data Params**

  None

* **Success Response:**

  * **Code:** 200 <br />
    **Content:**
    ```
    [
      {
          "UID": "16240",
          "user": "service_worker",
          "ip": "172.17.0.1",
          "lat": null,
          "lon": null,
          "mac": null,
          "created_date": "2020-09-14 05:29:18"
      },
    ....]
    ```

* **Error Response:**

  * **Code:** 200 <br />
    **Content:** `{ error : "Access Denied. No Token Present." }`

    OR

    * **Code:** 200 <br />
      **Content:** `{ error : "Access Denied. Invalid Token." }`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "/location/",
      dataType: "json",
      type : "GET",
      success : function(r) {
        console.log(r);
      }
    });
  ```
