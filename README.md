## HW8-DevSecOps-CI-CD

- Name: Tran Duc Tuan - VDT 2024
- Start dockers command: `sudo docker compose up --build
- URL access: `http://localhost:4000/`

## Vulnerability Analysis

### Unrestricted File Upload (/edit.php)

![](report_img/Pasted%20image%2020240517115433.png)

- In server side, it just check file's size to attacker and easy upload a php shell to server

```PHP
<?php phpinfo(); ?>
```

- Success upload shell  
![](report_img/Pasted%20image%2020240517120310.png)
- While user experience the web site, they can relize that user can access images which are rendered in client by access image in `/upload/`

![](report_img/Pasted%20image%2020240517120706.png)
- Access `shell.php` in web server to confirm the vulnerability

![](report_img/Pasted%20image%2020240517120823.png)

- To fix this vulnerability, i recommend a snippset of code to filter some params of file upload

```PHP
$allowed = [ 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif' ];

// Validate file name 
if (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|gif)$/', $fileName)) {
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
} 

// Validate file extension 
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); 
if (!array_key_exists($ext, $allowed)) { 
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
}

// Validate Content-Type type provided by the browser 
if ($fileType !== $allowed[$ext]) { 
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
}

// Validate MIME type using finfo
$finfo = finfo_open(FILEINFO_MIME_TYPE); 
$realMimeType = finfo_file($finfo, $fileTempName); 
finfo_close($finfo); 

if ($realMimeType !== $allowed[$ext]) { 
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
}
```

## Insecure Direct Object Reference (/edit.php)

![](report_img/Pasted%20image%2020240517231731.png)

- Server will get the data of jeager to edit by if while dont check that user own it or not. In database design, jaeger 1, 2 and 3 belong to admin

![](report_img/Pasted%20image%2020240517231943.png)

- Login with user1 credential

![](report_img/Pasted%20image%2020240517232209.png)

- Go to Provide, with user1's credential we can only get data of jaeger 4 & 5

![](report_img/Pasted%20image%2020240517232346.png)

- Try to edit Jeager 5

![](report_img/Pasted%20image%2020240517232510.png)

- Change param id to 1, we can access admin's jaeger

![](report_img/Pasted%20image%2020240517232542.png)

- To fix this vulnerability, i recommend a snippset of code to check user of jaeger id

```PHP
$cookieValue = base64_decode($_COOKIE['auth']);
list($username, $password) = explode(':', $cookieValue);

$query = "SELECT id FROM users WHERE username = ? AND user_password = ?";
$prepare_query = $conn->prepare($query);
$prepare_query->bind_param("ss", $username, $password);
$prepare_query->execute();
$result = $prepare_query->get_result();
$row = $result->fetch_assoc();

$jaeger = "SELECT * FROM jaegers WHERE id = ? AND user_id = ?";
```
