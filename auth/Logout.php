<?php
session_start();
session_destroy();
header('location: http://your-website.com');