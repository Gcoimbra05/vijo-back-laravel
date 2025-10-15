<?php

namespace App\Docs;

use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Vijo",
 *      description="Documentation for the Vijo system API",
 * )
 *
 * @OA\Post(
 *     path="/v2/resend_2fa",
 *     summary="Resend 2FA authentication code to user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"otp_id"},
 *                 @OA\Property(
 *                     property="otp_id",
 *                     type="integer",
 *                     description="ID of the OTP verification record"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="A new code has been resent to your email and/or mobile.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="otp_id", type="integer"),
 *                 @OA\Property(property="expires_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Session expired. Please log in again.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Authentication request not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User not authorized.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/send-reset-link",
 *     summary="Send password reset link to the provided email",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reset link sent to your email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Reset link sent to your email.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/password/reset",
 *     summary="Reset password and log in",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "token", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="token", type="string", example="generated-token"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login successful."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="refresh_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid token or email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid token or email.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/sign-up",
 *     summary="Register a new user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "email", "password", "confirm_password"},
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="password123"),
 *             @OA\Property(property="country_code", type="string", example="+1"),
 *             @OA\Property(property="mobile", type="string", example="5551234567"),
 *             @OA\Property(property="optInNewsUpdates", type="boolean", example=true),
 *             @OA\Property(property="timezone", type="string", example="America/New_York")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User registered successfully."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="otp_id", type="integer"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="A user with this email already exists.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="A user with this email already exists.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Password and confirm password do not match.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Password and confirm password do not match.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to send OTP.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to send OTP.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/logout",
 *     summary="Logout authenticated user",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logged out successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logged out successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/forgot-password",
 *     summary="Request password reset link",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset link sent to your email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Password reset link sent to your email.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Unable to send reset link. Please check the email address.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unable to send reset link. Please check the email address.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/reset-password",
 *     summary="Reset user password",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "token", "password", "password_confirmation"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="token", type="string", example="generated-token"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password has been reset successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Password has been reset successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid token or email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid token or email.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/verify-email",
 *     summary="Verify user email",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "hash"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="hash", type="string", example="sha1-of-email")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email verified successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Email verified successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid verification link.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid verification link.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/verify-email-resend",
 *     summary="Resend verification email to user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Verification email resent.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Verification email resent.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Email already verified.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Email already verified.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/sign-in",
 *     summary="Send 2FA code for user login (email or phone)",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"type"},
 *             @OA\Property(property="type", type="string", enum={"email", "phone"}, description="Type of login: email or phone"),
 *             @OA\Property(property="email", type="string", format="email", description="User email (required if type is email)"),
 *             @OA\Property(property="mobile", type="string", description="User mobile number (required if type is phone)"),
 *             @OA\Property(property="country_code", type="string", description="Country code (required if type is phone)"),
 *             @OA\Property(property="password", type="string", description="User password (optional)"),
 *             @OA\Property(property="trust_device", type="boolean", description="Trust this device for future logins"),
 *             @OA\Property(property="device_token", type="string", description="Device token for trusted device"),
 *             @OA\Property(property="device_name", type="string", description="Device name for trusted device")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Code sent or login successful (trusted device)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="otp_id", type="integer", nullable=true),
 *                 @OA\Property(property="skip_code", type="boolean"),
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="refresh_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean"),
 *                 @OA\Property(property="last_4_digits", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No account exists with the provided details.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid password.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/validate_2fa",
 *     summary="Validate 2FA code and log in user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"otp", "otp_id"},
 *             @OA\Property(property="otp", type="string", description="The 2FA code sent to the user"),
 *             @OA\Property(property="otp_id", type="integer", description="ID of the OTP verification record")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="refresh_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid, expired, or already used code.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/refresh-token",
 *     summary="Refresh the user's access token using a valid refresh token",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Token refreshed successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Token still valid. No new token generated.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string"),
 *                     @OA\Property(property="expires_in", type="integer")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized or invalid/expired token.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/validate-token",
 *     summary="Validate the user's access token",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"access_token"},
 *             @OA\Property(property="access_token", type="string", description="Access token to validate")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Token is valid.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="user_id", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized or invalid/expired token.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/countries",
 *     summary="Get list of supported countries with phone codes",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="List of countries returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="code", type="integer", example=1),
 *                     @OA\Property(property="label", type="string", example="US (+1)")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/onboarding-contents",
 *     summary="Get onboarding content for user registration and sign-in flows",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="Onboarding content returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="onboardingContents", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="pageSlug", type="string", example="home"),
 *                         @OA\Property(property="pageName", type="string", example="Home"),
 *                         @OA\Property(property="title", type="string", example="Your Life today is May 16"),
 *                         @OA\Property(property="subtitle", type="string", example="Let's VIJO!"),
 *                         @OA\Property(property="description", type="string", example="Capture life's precious moments with VIJO—your personal time capsule for memories, emotions, and growth."),
 *                         @OA\Property(property="message", type="string", example="")
 *                     )
 *                 ),
 *                 @OA\Property(property="onboardingEmoji", type="string", example="1f3a5")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/information-contents",
 *     summary="Get informational content for emotional outcomes and insights",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="Information content returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="informationContents", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="pageSlug", type="string", example="emotional_outcome"),
 *                         @OA\Property(property="pageName", type="string", example="Emotional Outcome"),
 *                         @OA\Property(property="title", type="string", example="Emotional Outcome"),
 *                         @OA\Property(property="description", type="string", example="Emotional intelligence (EQ) is the ability to understand, interpret, and control emotions to better communicate and relate to others constructively.")
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/static-pages",
 *     summary="Get static pages content such as FAQs and privacy policy",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="Static pages content returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="faqs", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="title", type="string", example="Why Video Journaling?"),
 *                         @OA\Property(property="contents", type="string", example="Video Journaling is a safe, secure and easy way for people to reflect on meaningful experiences to document their life.")
 *                     )
 *                 ),
 *                 @OA\Property(property="privacy_policy", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="name", type="string", example="Privacy Policy 1.0")
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/shared-video-details/{id}",
 *     summary="Get details of a shared video journal by ID",
 *     tags={"Video Journal"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the shared video journal",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Shared journal details returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="journal_data", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=123),
 *                         @OA\Property(property="journal_title", type="string", example="My Journal Title"),
 *                         @OA\Property(property="category_name", type="string", example="Work"),
 *                         @OA\Property(property="journal_tags", type="string", example="1,2,3"),
 *                         @OA\Property(property="is_private", type="integer", example=0),
 *                         @OA\Property(property="video", type="string", example="https://example.com/video.mp4"),
 *                         @OA\Property(property="video_thumb", type="string", example="https://example.com/thumb.jpg"),
 *                         @OA\Property(property="user_tags", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"))),
 *                         @OA\Property(property="emotions", type="object"),
 *                         @OA\Property(property="transcription", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="answer", type="string"), @OA\Property(property="thumb", type="string"), @OA\Property(property="emoji", type="string"), @OA\Property(property="emotion_score", type="number"), @OA\Property(property="text", type="string"), @OA\Property(property="emotion", type="string"))),
 *                         @OA\Property(property="final_video_transcript", type="string"),
 *                         @OA\Property(property="summaryReport", type="string"),
 *                         @OA\Property(property="video_type_id", type="string"),
 *                         @OA\Property(property="catalog_id", type="string"),
 *                         @OA\Property(property="catalog_name", type="string"),
 *                         @OA\Property(property="created_at", type="string", example="Sep 23, 2025"),
 *                         @OA\Property(property="contact", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="first_name", type="string"), @OA\Property(property="last_name", type="string"), @OA\Property(property="email", type="string"), @OA\Property(property="mobile", type="string")),
 *                         @OA\Property(property="group", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"))
 *                     )
 *                 ),
 *                 @OA\Property(property="contacts", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="first_name", type="string"), @OA\Property(property="last_name", type="string"), @OA\Property(property="email", type="string"), @OA\Property(property="mobile", type="string"))),
 *                 @OA\Property(property="groups", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string")))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Id parameter is required.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Id parameter is required"),
 *             @OA\Property(property="results", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Journal not found or access denied.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Journal not found or access denied."),
 *             @OA\Property(property="results", type="null")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/response-request-details/{sharedId}",
 *     summary="Get details for a shared video request",
 *     description="Returns detailed information about a video request using a shared (base64) ID. Includes catalog info, record times, video type metrics, and related questions.",
 *     tags={"Video Requests"},
 *     @OA\Parameter(
 *         name="sharedId",
 *         in="path",
 *         required=true,
 *         description="Base64 encoded ID of the shared video request",
 *         @OA\Schema(type="string", example="MTAwMQ==")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Video request details retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="parent_catalog_id", type="string", example="0"),
 *                 @OA\Property(property="catalog_id", type="string", example="1001"),
 *                 @OA\Property(property="request_id", type="integer", example=1234),
 *                 @OA\Property(property="record_date", type="string", format="date", example="2025-10-07"),
 *                 @OA\Property(property="video_types", type="object",
 *                     @OA\Property(property="metrics", type="integer", example=6),
 *                     @OA\Property(property="kpis", type="integer", example=2),
 *                     @OA\Property(property="kpi_metrics", type="integer", example=3)
 *                 ),
 *                 @OA\Property(property="video_type_id", type="integer", example=1),
 *                 @OA\Property(property="min_record_time", type="string", example="15"),
 *                 @OA\Property(property="record_time", type="string", example="60"),
 *                 @OA\Property(
 *                     property="questions",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Metric Name"),
 *                         @OA\Property(property="question", type="string", example="What is your score?"),
 *                         @OA\Property(property="video_question", type="string", example="Describe your experience."),
 *                         @OA\Property(property="range", type="string", example="1-10")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or missing sharedId parameter.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Id parameter is required"),
 *             @OA\Property(property="results", type="object", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized access to this request.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized access to this request.")
 *         )
 *     )
 * )
 *
 *  * @OA\Get(
 *     path="/v2/users",
 *     summary="List all users",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="List of users",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Smith"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="country_code", type="string", example="+1"),
 *                 @OA\Property(property="mobile", type="string", example="5551234567")
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/v2/users/{id}",
 *     summary="Get user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
*     @OA\Response(
*         response=200,
*         description="User found",
*         @OA\JsonContent(
*             @OA\Property(property="id", type="integer", example=1),
*             @OA\Property(property="first_name", type="string", example="John"),
*             @OA\Property(property="last_name", type="string", example="Smith"),
*             @OA\Property(property="email", type="string", example="john@example.com"),
*             @OA\Property(property="country_code", type="string", example="+1"),
*             @OA\Property(property="mobile", type="string", example="5551234567")
*         )
*     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
*
 * @OA\Post(
 *     path="/v2/users",
 *     summary="Create a new user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "email", "password", "confirm_password"},
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="password123"),
 *             @OA\Property(property="country_code", type="string", example="+1"),
 *             @OA\Property(property="mobile", type="string", example="5551234567"),
 *             @OA\Property(property="optInNewsUpdates", type="boolean", example=true),
 *             @OA\Property(property="timezone", type="string", example="America/New_York")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User registered successfully."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="first_name", type="string", example="John"),
 *                     @OA\Property(property="last_name", type="string", example="Smith"),
 *                     @OA\Property(property="email", type="string", example="john@example.com"),
 *                     @OA\Property(property="country_code", type="string", example="+1"),
 *                     @OA\Property(property="mobile", type="string", example="5551234567")
 *                 ),
 *                 @OA\Property(property="otp_id", type="integer", example=12345),
 *                 @OA\Property(property="expires_in", type="integer", example=3600),
 *                 @OA\Property(property="loggedIn", type="boolean", example=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="A user with this email or phone already exists.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="A user with this email already exists.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Password and confirm password do not match.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Password and confirm password do not match.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to send OTP.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to send OTP.")
 *         )
 *     )
 * ) 
*
 * @OA\Put(
 *     path="/v2/users/{id}",
 *     summary="Update user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword123"),
 *             @OA\Property(property="country_code", type="string", example="+1"),
 *             @OA\Property(property="mobile", type="string", example="5551234567"),
 *             @OA\Property(property="reminders", type="boolean", example=true),
 *             @OA\Property(property="notifications", type="boolean", example=true),
 *             @OA\Property(property="timezone", type="string", example="America/New_York"),
 *             @OA\Property(property="optInNewsUpdates", type="boolean", example=true),
 *             @OA\Property(property="two_factor_enabled", type="boolean", example=true)
 *         )
 *     ),
*     @OA\Response(
*         response=200,
*         description="User updated successfully.",
*         @OA\JsonContent(
*             @OA\Property(property="id", type="integer", example=1),
*             @OA\Property(property="first_name", type="string", example="John"),
*             @OA\Property(property="last_name", type="string", example="Smith"),
*             @OA\Property(property="email", type="string", example="john@example.com"),
*             @OA\Property(property="country_code", type="string", example="+1"),
*             @OA\Property(property="mobile", type="string", example="5551234567")
*         )
*     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/v2/users/{id}",
 *     summary="Delete user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/user/feedback",
 *     summary="Submit user feedback",
 *     description="Allows an authenticated user to submit feedback. Sends an email notification to support and stores the feedback in the database.",
 *     tags={"User Feedback"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"type", "message"},
 *             @OA\Property(property="type", type="string", example="bug", description="Type of feedback (e.g. bug, suggestion, other)"),
 *             @OA\Property(property="message", type="string", example="I found a bug in the dashboard.", description="Feedback message"),
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User email (optional)"),
 *             @OA\Property(property="subject", type="string", example="Dashboard Bug", description="Feedback subject (optional)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Feedback submitted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Feedback submitted successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=123),
 *                 @OA\Property(property="type", type="string", example="bug"),
 *                 @OA\Property(property="message", type="string", example="I found a bug in the dashboard."),
 *                 @OA\Property(property="email", type="string", example="user@example.com"),
 *                 @OA\Property(property="subject", type="string", example="Dashboard Bug"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-07T12:34:56Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="type", type="array", @OA\Items(type="string", example="The type field is required.")),
 *                 @OA\Property(property="message", type="array", @OA\Items(type="string", example="The message field is required."))
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/v2/user/delete-account",
 *     summary="Permanently delete the authenticated user's account and all related data",
 *     description="Deletes the authenticated user's account and all associated records (videos, feedback, contacts, etc). This action is irreversible.",
 *     tags={"Users"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Account and all user data deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Account and all user data deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error deleting account.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Error deleting account: [error details]")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/dashboard",
 *     summary="Get dashboard data for the authenticated user",
 *     description="Returns a comprehensive set of dashboard data for the authenticated user, including activity, categories, daily messages, guided tours, membership plans, journals, quick goals, and more.",
 *     tags={"Users"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Dashboard data retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="activity", type="object"),
 *                 @OA\Property(property="categories", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="coming_soon", type="object"),
 *                 @OA\Property(property="current_date", type="string", example="2025-10-07"),
 *                 @OA\Property(property="currentDate", type="string", example="Tuesday, October 7, 2025"),
 *                 @OA\Property(property="daily_message", type="object"),
 *                 @OA\Property(property="filterByLabels", type="object"),
 *                 @OA\Property(property="graphTypes", type="object"),
 *                 @OA\Property(property="greeting", type="string", example="Good morning, John!"),
 *                 @OA\Property(property="guidedTours", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="guidedToursTaken", type="integer", example=1),
 *                 @OA\Property(property="insightFilters", type="object"),
 *                 @OA\Property(property="membershipPlan", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="myJournals", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="plans", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="promotionalCatalogs", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="quick_goals", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="rangeTypeLabels", type="object"),
 *                 @OA\Property(property="responceCount", type="object"),
 *                 @OA\Property(property="timezoneMenus", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="userPlan", type="object"),
 *                 @OA\Property(property="userTags", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="vijo_of_day", type="object"),
 *                 @OA\Property(property="viewByLabels", type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/emotional-snapshot",
 *     summary="Get emotional snapshot for the authenticated user",
 *     description="Returns the latest emotional snapshot for the authenticated user, including the last Vijo (journal) title and date.",
 *     tags={"Emlo"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Emotional snapshot retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Emotional snapshot retrieved successfully."),
 *             @OA\Property(property="data", type="object", description="Snapshot data (structure depends on implementation)"),
 *             @OA\Property(property="last_vijo", type="object",
 *                 @OA\Property(property="title", type="string", example="My Journal Title"),
 *                 @OA\Property(property="date", type="string", example="Oct 07, 2025")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/update-guided-tours",
 *     summary="Update guided tours status for the authenticated user",
 *     description="Updates the guided tours status (taken or not) for the authenticated user.",
 *     tags={"Users"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"guided_tours"},
 *             @OA\Property(property="guided_tours", type="integer", enum={0,1}, example=1, description="Guided tours status: 0 (not taken), 1 (taken)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Guided tour updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Guided tour updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="guided_tours", type="array", @OA\Items(type="string", example="The guided_tours field is required."))
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/subscription-plans",
 *     summary="Get available subscription plans and the user's current plan",
 *     tags={"User"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of available subscription plans and user's current plan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="plans", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="slug", type="string", example="basic"),
 *                         @OA\Property(property="title", type="string", example="Basic Plan"),
 *                         @OA\Property(property="description", type="string", example="Access to basic features"),
 *                         @OA\Property(property="paymentLink", type="string", example="https://payment.example.com/plan/basic")
 *                     )
 *                 ),
 *                 @OA\Property(property="userPlan", type="object",
 *                     @OA\Property(property="id", type="string", example="2"),
 *                     @OA\Property(property="slug", type="string", example="premium"),
 *                     @OA\Property(property="title", type="string", example="Premium Plan"),
 *                     @OA\Property(property="description", type="string", example="Access to all premium features")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/membership-plans",
 *     summary="Get all available membership plans",
 *     tags={"Membership Plans"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of available membership plans",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plans", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Basic"),
 *                         @OA\Property(property="description", type="string", example="Basic plan features"),
 *                         @OA\Property(property="slug", type="string", example="basic")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No membership plans available",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="No membership plans available."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plans", type="array", @OA\Items())
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/membership-plans/{membership_plan}",
 *     summary="Get details of a specific membership plan",
 *     tags={"Membership Plans"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="membership_plan",
 *         in="path",
 *         required=true,
 *         description="ID of the membership plan",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Membership plan details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plan", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Basic"),
 *                     @OA\Property(property="description", type="string", example="Basic plan features"),
 *                     @OA\Property(property="slug", type="string", example="basic")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Membership plan not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Membership plan not found."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plan", type="null")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/video-requests/{video_request}",
 *     summary="Retrieve a specific video request by ID",
 *     tags={"Video Requests"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="video_request",
 *         in="path",
 *         required=true,
 *         description="ID of the video request",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Video request retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Video request retrieved successfully."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Video request not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Video request not found."),
 *             @OA\Property(property="data", type="object", nullable=true)
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/video-requests",
 *     summary="Create a new video request",
 *     tags={"Video Requests"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"catalog_id"},
 *                 @OA\Property(property="catalog_id", type="integer", example=1, description="ID of the catalog"),
 *                 @OA\Property(property="file", type="string", format="binary", description="Video file (optional)"),
 *                 @OA\Property(property="video_duration", type="integer", example=120, description="Duration of the video in seconds (optional)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Video request created successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Video request created successfully."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation error."),
 *             @OA\Property(property="data", type="object", nullable=true)
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/video-galleries",
 *     summary="Get all video galleries for the authenticated user",
 *     tags={"Video Requests"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of video galleries retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="user_id", type="integer", example=1),
 *                     @OA\Property(property="journal_title", type="string", example="My Journal"),
 *                     @OA\Property(property="ref_user_id", type="integer", example=2),
 *                     @OA\Property(property="journal_type", type="string", example="request"),
 *                     @OA\Property(property="recommendation_id", type="string", example=""),
 *                     @OA\Property(property="category_name", type="string", example="Wellness"),
 *                     @OA\Property(property="is_private", type="integer", example=0),
 *                     @OA\Property(property="rrc_video1", type="string", example="video.mp4"),
 *                     @OA\Property(property="rrc_video1_thumb", type="string", example="thumb.jpg"),
 *                     @OA\Property(property="video", type="string", example="https://example.com/video.mp4"),
 *                     @OA\Property(property="video_thumb", type="string", example="https://example.com/thumb.jpg"),
 *                     @OA\Property(property="recordedBy", type="string", example="self"),
 *                     @OA\Property(property="parent_catalog_id", type="integer", example=0),
 *                     @OA\Property(property="cp_id", type="integer", example=0),
 *                     @OA\Property(property="created_at", type="string", example="Oct 10, 2025 14:30"),
 *                     @OA\Property(property="date", type="string", example="Oct 10, 2025 2:30PM"),
 *                     @OA\Property(property="mediaId", type="integer", example=10),
 *                     @OA\Property(property="catalogEmoji", type="string", example="😊"),
 *                     @OA\Property(property="user_name", type="string", example="John Smith"),
 *                     @OA\Property(property="tags", type="array", @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Motivation")
 *                     ))
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/v2/video-detail/{id}",
 *     summary="Get detailed information about a specific video request (journal)",
 *     tags={"Video Requests"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the video request (journal)",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Journal details retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="journal_data", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="catalog_id", type="integer", example=1),
 *                         @OA\Property(property="catalog_name", type="string", example="Daily Reflection"),
 *                         @OA\Property(property="catalog_emoji", type="string", example="😊"),
 *                         @OA\Property(property="category_name", type="string", example="Wellness"),
 *                         @OA\Property(property="contacts", type="array", @OA\Items(
 *                             @OA\Property(property="contact_id", type="integer", example=1),
 *                             @OA\Property(property="first_name", type="string", example="John"),
 *                             @OA\Property(property="last_name", type="string", example="Smith"),
 *                             @OA\Property(property="email", type="string", example="john@example.com"),
 *                             @OA\Property(property="mobile", type="string", example="5551234567")
 *                         )),
 *                         @OA\Property(property="groups", type="array", @OA\Items(
 *                             @OA\Property(property="group_id", type="integer", example=1),
 *                             @OA\Property(property="group_name", type="string", example="Friends")
 *                         )),
 *                         @OA\Property(property="created_at", type="string", example="Oct 10, 2025 2:30PM"),
 *                         @OA\Property(property="cred_score", type="integer", example=75),
 *                         @OA\Property(property="perceived_score", type="integer", example=80),
 *                         @OA\Property(property="actual_score", type="integer", example=78),
 *                         @OA\Property(property="emotional_insights", type="array", @OA\Items(type="object")),
 *                         @OA\Property(property="final_video_transcript", type="string", example="Today was a really productive day at work..."),
 *                         @OA\Property(property="gptSummary", type="string", example="Summary generated by GPT"),
 *                         @OA\Property(property="is_emotional_category", type="boolean", example=true),
 *                         @OA\Property(property="is_private", type="integer", example=0),
 *                         @OA\Property(property="journal_tags", type="string", example="motivation,work"),
 *                         @OA\Property(property="journal_title", type="string", example="My Journal"),
 *                         @OA\Property(property="journal_type", type="string", example="daily"),
 *                         @OA\Property(property="recommendation_id", type="string", example=""),
 *                         @OA\Property(property="recordedBy", type="string", example="self"),
 *                         @OA\Property(property="ref_user_id", type="integer", example=2),
 *                         @OA\Property(property="rrc_video1", type="string", example="video.mp4"),
 *                         @OA\Property(property="rrc_video1_thumb", type="string", example="thumb.jpg"),
 *                         @OA\Property(property="suggested_catalogs", type="array", @OA\Items(type="object")),
 *                         @OA\Property(property="summaryReport", type="object",
 *                             @OA\Property(property="key_points", type="array", @OA\Items(type="string")),
 *                             @OA\Property(property="mood_analysis", type="string"),
 *                             @OA\Property(property="time_references", type="object")
 *                         ),
 *                         @OA\Property(property="transcription", type="array", @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="answer", type="string", example="Breakthrough at work"),
 *                             @OA\Property(property="thumb", type="string", example="https://placehold.co/300x200/0066cc/ffffff?text=Work+Day"),
 *                             @OA\Property(property="emoji", type="string", example="U+1F4AA"),
 *                             @OA\Property(property="emotion_score", type="number", format="float", example=0.85),
 *                             @OA\Property(property="text", type="string", example="Breakthrough at work"),
 *                             @OA\Property(property="emotion", type="string", example="proud")
 *                         )),
 *                         @OA\Property(property="user_id", type="integer", example=1),
 *                         @OA\Property(property="user_tags", type="array", @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Motivation")
 *                         )),
 *                         @OA\Property(property="video", type="string", example="https://example.com/video.mp4"),
 *                         @OA\Property(property="video_thumb", type="string", example="https://example.com/thumb.jpg"),
 *                         @OA\Property(property="video_type_id", type="integer", example=1)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Id parameter is required.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Id parameter is required"),
 *             @OA\Property(property="results", type="object", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Journal not found or access denied.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Journal not found or access denied."),
 *             @OA\Property(property="results", type="object", nullable=true)
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/skip-vijo",
 *     summary="Mark a catalog as skipped for the authenticated user",
 *     tags={"Catalogs"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"catalog_id"},
 *             @OA\Property(property="catalog_id", type="integer", example=1, description="ID of the catalog to skip")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Vijo skipped and saved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Vijo skipped and saved successfully."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="catalog_id", type="integer", example=1),
 *                 @OA\Property(property="skipped_at", type="string", format="date", example="2025-10-10")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation error."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/validate-profile",
 *     summary="Validate user profile change and send verification code",
 *     description="Validates the user's current password and sends a verification code to the new email or phone number before allowing profile updates. This is a security measure to verify ownership of the new contact information.",
 *     tags={"Profile and Security"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"type", "password"},
 *                 @OA\Property(
 *                     property="type",
 *                     type="string",
 *                     enum={"email", "phone"},
 *                     description="Type of profile information to validate and update",
 *                     example="email"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     format="password",
 *                     description="User's current password for verification",
 *                     example="currentPassword123"
 *                 ),
 *                 @OA\Property(
 *                     property="new_email",
 *                     type="string",
 *                     format="email",
 *                     description="New email address (required when type=email)",
 *                     example="newemail@example.com"
 *                 ),
 *                 @OA\Property(
 *                     property="country_code",
 *                     type="string",
 *                     description="Country code for phone number (required when type=phone)",
 *                     example="+1"
 *                 ),
 *                 @OA\Property(
 *                     property="mobile",
 *                     type="string",
 *                     description="New mobile phone number (required when type=phone)",
 *                     example="5551234567"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Verification code sent successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the request was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Verification code has been successfully sent to your mobile number.",
 *                 description="Success message indicating where the code was sent"
 *             ),
 *             @OA\Property(
 *                 property="results",
 *                 type="object",
 *                 @OA\Property(
 *                     property="otp_id",
 *                     type="integer",
 *                     example=123,
 *                     description="ID of the verification record, needed for subsequent validation"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid password provided",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Incorrect password."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="User not authenticated."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The given data was invalid."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="type",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The type field is required."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The password field is required."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="new_email",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The new email field is required when type is email."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="country_code",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The country code field is required when type is phone."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="mobile",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The mobile field is required when type is phone."
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error (e.g., email sending failure)",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Internal server error"
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/update-profile",
 *     summary="Update user profile information after verification",
 *     description="Updates the user's email or phone number after validating the verification code sent during the validate-profile step. This completes the secure profile update process.",
 *     tags={"Profile and Security"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"type", "password", "confirmation_code", "otp_id"},
 *                 @OA\Property(
 *                     property="type",
 *                     type="string",
 *                     enum={"email", "phone"},
 *                     description="Type of profile information being updated",
 *                     example="email"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     format="password",
 *                     description="User's current password for verification",
 *                     example="currentPassword123"
 *                 ),
 *                 @OA\Property(
 *                     property="confirmation_code",
 *                     type="string",
 *                     description="6-digit verification code received via email or SMS",
 *                     example="123456"
 *                 ),
 *                 @OA\Property(
 *                     property="otp_id",
 *                     type="integer",
 *                     description="ID of the OTP verification record from validate-profile response",
 *                     example=123
 *                 ),
 *                 @OA\Property(
 *                     property="new_email",
 *                     type="string",
 *                     format="email",
 *                     description="New email address (required when type=email)",
 *                     example="newemail@example.com"
 *                 ),
 *                 @OA\Property(
 *                     property="country_code",
 *                     type="string",
 *                     description="Country code for new phone number (required when type=phone)",
 *                     example="+1"
 *                 ),
 *                 @OA\Property(
 *                     property="mobile",
 *                     type="string",
 *                     description="New mobile phone number (required when type=phone)",
 *                     example="5551234567"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the profile update was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Profile updated successfully.",
 *                 description="Success confirmation message"
 *             ),
 *             @OA\Property(
 *                 property="results",
 *                 type="object",
 *                 description="Updated user data",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Smith"),
 *                 @OA\Property(property="email", type="string", example="newemail@example.com"),
 *                 @OA\Property(property="country_code", type="string", example="+1"),
 *                 @OA\Property(property="mobile", type="string", example="5551234567"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid verification code, expired, already used, or incorrect password",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 oneOf={
 *                     @OA\Schema(type="string", example="The provided code is invalid, expired, or has already been used."),
 *                     @OA\Schema(type="string", example="Incorrect password.")
 *                 },
 *                 description="Error message indicating the specific issue"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="User not authenticated."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The given data was invalid."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="type",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The type field is required."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The password field is required."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="confirmation_code",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The confirmation code field is required."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="otp_id",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The otp id field is required."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="new_email",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The new email field is required when type is email."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="country_code",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The country code field is required when type is phone."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="mobile",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The mobile field is required when type is phone."
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * * @OA\Post(
 *     path="/v2/save-new-password",
 *     summary="Change user password with current password verification",
 *     description="Allows an authenticated user to change their password by providing their current password and a new password. The new password must be at least 8 characters long and confirmed.",
 *     tags={"Profile and Security"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"current_password", "new_password", "new_password_confirmation"},
 *                 @OA\Property(
 *                     property="current_password",
 *                     type="string",
 *                     format="password",
 *                     description="User's current password for verification",
 *                     example="currentPassword123"
 *                 ),
 *                 @OA\Property(
 *                     property="new_password",
 *                     type="string",
 *                     format="password",
 *                     minLength=8,
 *                     description="New password (minimum 8 characters)",
 *                     example="newSecurePassword456"
 *                 ),
 *                 @OA\Property(
 *                     property="new_password_confirmation",
 *                     type="string",
 *                     format="password",
 *                     description="Confirmation of the new password (must match new_password)",
 *                     example="newSecurePassword456"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password changed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the password change was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Password changed successfully.",
 *                 description="Success confirmation message"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Current password is incorrect",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Current password is incorrect."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="User not authenticated."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The given data was invalid."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="current_password",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The current password field is required."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="new_password",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         oneOf={
 *                             @OA\Schema(type="string", example="The new password field is required."),
 *                             @OA\Schema(type="string", example="The new password must be at least 8 characters."),
 *                             @OA\Schema(type="string", example="The new password confirmation does not match.")
 *                         }
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="new_password_confirmation",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The new password confirmation field is required."
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/v2/update-2fa",
 *     summary="Enable or disable two-factor authentication for user",
 *     description="Allows an authenticated user to enable or disable two-factor authentication (2FA) for their account. This is a security feature that adds an extra layer of protection.",
 *     tags={"Profile and Security"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"enabled"},
 *                 @OA\Property(
 *                     property="enabled",
 *                     type="boolean",
 *                     description="Enable (true) or disable (false) two-factor authentication",
 *                     example=true
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="2FA setting updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the 2FA update was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="2FA updated.",
 *                 description="Success confirmation message"
 *             ),
 *             @OA\Property(
 *                 property="results",
 *                 type="object",
 *                 @OA\Property(
 *                     property="enabled",
 *                     type="boolean",
 *                     example=true,
 *                     description="Current 2FA status after update"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not found or not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="User not found"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The given data was invalid."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="enabled",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         oneOf={
 *                             @OA\Schema(type="string", example="The enabled field is required."),
 *                             @OA\Schema(type="string", example="The enabled field must be true or false.")
 *                         }
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/v2/share-video-contacts",
 *     summary="Share video journal with contacts and groups",
 *     description="Shares an existing video journal with selected contacts and/or contact groups. Creates new VideoRequest records with type 'share' and sends notifications via email and SMS to the recipients.",
 *     tags={"Video Sharing"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"request_id"},
 *                 @OA\Property(
 *                     property="contact_ids",
 *                     type="array",
 *                     nullable=true,
 *                     description="Array of contact IDs to share with",
 *                     @OA\Items(type="integer", example=1)
 *                 ),
 *                 @OA\Property(
 *                     property="group_ids",
 *                     type="array",
 *                     nullable=true,
 *                     description="Array of contact group IDs to share with",
 *                     @OA\Items(type="integer", example=1)
 *                 ),
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="integer",
 *                     minimum=1,
 *                     description="ID of the original video request to share",
 *                     example=123
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Video shared successfully with contacts",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the sharing was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Video shared successfully with your contacts.",
 *                 description="Success confirmation message"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Original video request not found",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Original video request not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The given data was invalid."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         oneOf={
 *                             @OA\Schema(type="string", example="The request id field is required."),
 *                             @OA\Schema(type="string", example="The selected request id is invalid."),
 *                             @OA\Schema(type="string", example="The request id must be at least 1.")
 *                         }
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="contact_ids",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         oneOf={
 *                             @OA\Schema(type="string", example="The contact_ids must be an array."),
 *                             @OA\Schema(type="string", example="The selected contact_ids.0 is invalid.")
 *                         }
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="group_ids",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         oneOf={
 *                             @OA\Schema(type="string", example="The group_ids must be an array."),
 *                             @OA\Schema(type="string", example="The selected group_ids.0 is invalid.")
 *                         }
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Unauthenticated."
 *             )
 *         )
 *     )
 * )
 * 
  * @OA\Get(
 *     path="/v2/contacts",
 *     summary="Get contacts and groups for the authenticated user",
 *     description="Retrieves a paginated list of contacts and/or contact groups for the authenticated user. Supports filtering by type (contacts, groups, or all) and search functionality.",
 *     tags={"Contacts"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Filter by type: 'contacts', 'groups', or 'all'",
 *         required=false,
 *         @OA\Schema(type="string", enum={"contacts", "groups", "all"}, default="all")
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search term to filter contacts/groups by name, email, or mobile",
 *         required=false,
 *         @OA\Schema(type="string", example="john")
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of items per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=15, example=10)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer", default=1, example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Contacts and groups retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Contacts and groups retrieved successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     oneOf={
 *                         @OA\Schema(
 *                             type="object",
 *                             description="Contact object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="user_id", type="integer", example=1),
 *                             @OA\Property(property="first_name", type="string", example="John"),
 *                             @OA\Property(property="last_name", type="string", example="Smith"),
 *                             @OA\Property(property="email", type="string", example="john@example.com"),
 *                             @OA\Property(property="country_code", type="string", example="+1"),
 *                             @OA\Property(property="mobile", type="string", example="5551234567"),
 *                             @OA\Property(property="created_at", type="string", format="date-time"),
 *                             @OA\Property(property="updated_at", type="string", format="date-time")
 *                         ),
 *                         @OA\Schema(
 *                             type="object",
 *                             description="Group object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Friends")
 *                         )
 *                     }
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/contacts/{id}",
 *     summary="Get a specific contact by ID",
 *     description="Retrieves detailed information about a specific contact, including associated groups. Only returns contacts that belong to the authenticated user.",
 *     tags={"Contacts"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Contact ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Contact retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Contact retrieved successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Smith"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="country_code", type="string", example="+1"),
 *                 @OA\Property(property="mobile", type="string", example="5551234567"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time"),
 *                 @OA\Property(
 *                     property="groups",
 *                     type="array",
 *                     description="Groups this contact belongs to",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Friends")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Contact not found or does not belong to user",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Contact not found or does not belong to the user."),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/contacts",
 *     summary="Create a new contact",
 *     description="Creates a new contact for the authenticated user. Optionally assigns the contact to one or more groups. Prevents duplicate contacts with the same phone number.",
 *     tags={"Contacts"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"first_name", "last_name", "mobile"},
 *                 @OA\Property(property="first_name", type="string", maxLength=255, example="John", description="Contact's first name"),
 *                 @OA\Property(property="last_name", type="string", maxLength=255, example="Smith", description="Contact's last name"),
 *                 @OA\Property(property="mobile", type="string", maxLength=15, example="5551234567", description="Contact's mobile phone number"),
 *                 @OA\Property(property="country_code", type="string", maxLength=10, example="+1", description="Country code (defaults to '1' if not provided)", nullable=true),
 *                 @OA\Property(property="email", type="string", format="email", maxLength=255, example="john@example.com", description="Contact's email address", nullable=true),
 *                 @OA\Property(
 *                     property="groups",
 *                     type="array",
 *                     description="Array of group IDs to assign this contact to",
 *                     @OA\Items(type="integer", example=1),
 *                     nullable=true
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Contact created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Contact created successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Smith"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="country_code", type="string", example="+1"),
 *                 @OA\Property(property="mobile", type="string", example="5551234567"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Contact with this phone number already exists or invalid group ID",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 oneOf={
 *                     @OA\Schema(type="string", example="A contact with this phone number already exists."),
 *                     @OA\Schema(type="string", example="Group ID 5 does not belong to the user.")
 *                 }
 *             ),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="first_name",
 *                     type="array",
 *                     @OA\Items(type="string", example="The first name field is required.")
 *                 ),
 *                 @OA\Property(
 *                     property="last_name",
 *                     type="array",
 *                     @OA\Items(type="string", example="The last name field is required.")
 *                 ),
 *                 @OA\Property(
 *                     property="mobile",
 *                     type="array",
 *                     @OA\Items(type="string", example="The mobile field is required.")
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="array",
 *                     @OA\Items(type="string", example="The email must be a valid email address.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Put(
 *     path="/v2/contacts/{id}",
 *     summary="Update an existing contact",
 *     description="Updates an existing contact's information and group associations. Only allows updating contacts that belong to the authenticated user.",
 *     tags={"Contacts"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Contact ID to update",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"first_name", "last_name", "mobile"},
 *                 @OA\Property(property="first_name", type="string", maxLength=255, example="John", description="Contact's first name"),
 *                 @OA\Property(property="last_name", type="string", maxLength=255, example="Smith", description="Contact's last name"),
 *                 @OA\Property(property="mobile", type="string", maxLength=15, example="5551234567", description="Contact's mobile phone number"),
 *                 @OA\Property(property="country_code", type="string", maxLength=10, example="+1", description="Country code", nullable=true),
 *                 @OA\Property(property="email", type="string", format="email", maxLength=255, example="john@example.com", description="Contact's email address", nullable=true),
 *                 @OA\Property(
 *                     property="groups",
 *                     type="array",
 *                     description="Array of group IDs to assign this contact to (replaces existing groups)",
 *                     @OA\Items(type="integer", example=1),
 *                     nullable=true
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Contact updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Contact updated successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Smith"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="country_code", type="string", example="+1"),
 *                 @OA\Property(property="mobile", type="string", example="5551234567"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid group ID provided",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Group ID 5 does not belong to the user."),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Contact not found or does not belong to user",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Contact not found or does not belong to the user."),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="first_name",
 *                     type="array",
 *                     @OA\Items(type="string", example="The first name field is required.")
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="array",
 *                     @OA\Items(type="string", example="The email must be a valid email address.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/v2/contacts/{id}",
 *     summary="Delete a contact",
 *     description="Permanently deletes a contact and removes it from all associated groups. Only allows deleting contacts that belong to the authenticated user.",
 *     tags={"Contacts"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Contact ID to delete",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Contact deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Contact deleted successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1, description="ID of the deleted contact")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Contact not found or does not belong to user",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Contact not found or does not belong to the user."),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete contact",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to delete contact."),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/contacts/multiple",
 *     summary="Create multiple contacts in batch",
 *     description="Creates multiple contacts in a single request for the authenticated user. Automatically detects and skips duplicate contacts based on phone number. Provides feedback on successful creations and duplicates.",
 *     tags={"Contacts"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"first_name", "last_name", "mobile"},
 *                     @OA\Property(property="first_name", type="string", maxLength=255, example="John", description="Contact's first name"),
 *                     @OA\Property(property="last_name", type="string", maxLength=255, example="Smith", description="Contact's last name"),
 *                     @OA\Property(property="mobile", type="string", maxLength=15, example="5551234567", description="Contact's mobile phone number"),
 *                     @OA\Property(property="country_code", type="string", maxLength=10, example="+1", description="Country code (defaults to '1' if not provided)", nullable=true),
 *                     @OA\Property(property="email", type="string", format="email", maxLength=255, example="john@example.com", description="Contact's email address", nullable=true)
 *                 )
 *             ),
 *             example={
 *                 {
 *                     "first_name": "John",
 *                     "last_name": "Smith",
 *                     "mobile": "5551234567",
 *                     "country_code": "+1",
 *                     "email": "john@example.com"
 *                 },
 *                 {
 *                     "first_name": "Jane",
 *                     "last_name": "Doe",
 *                     "mobile": "5559876543",
 *                     "email": "jane@example.com"
 *                 }
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Contacts created successfully (may include duplicate information)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 oneOf={
 *                     @OA\Schema(type="string", example="Contacts created successfully."),
 *                     @OA\Schema(type="string", example="Contacts created successfully. Some contacts already existed and were not added again.")
 *                 },
 *                 description="Success message indicating creation status"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="created",
 *                     type="array",
 *                     description="Array of successfully created contact data",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="first_name", type="string", example="John"),
 *                         @OA\Property(property="last_name", type="string", example="Smith"),
 *                         @OA\Property(property="mobile", type="string", example="5551234567"),
 *                         @OA\Property(property="country_code", type="string", example="+1"),
 *                         @OA\Property(property="email", type="string", example="john@example.com"),
 *                         @OA\Property(property="user_id", type="integer", example=1)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing required fields in one or more contacts",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 oneOf={
 *                     @OA\Schema(type="string", example="The first_name field is required."),
 *                     @OA\Schema(type="string", example="The last_name field is required."),
 *                     @OA\Schema(type="string", example="The mobile field is required.")
 *                 }
 *             ),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/v2/save-video-request",
 *     summary="Save and update video request with journal information",
 *     description="Updates an existing video request with journal title, tags, and privacy settings. Processes tags by category and handles tag creation if needed. This endpoint is used to finalize a video journal after recording.",
 *     tags={"Video Requests"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"request_id"},
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="integer",
 *                     description="ID of the video request to update",
 *                     example=123
 *                 ),
 *                 @OA\Property(
 *                     property="journal_name",
 *                     type="string",
 *                     description="Title/name for the journal entry",
 *                     example="My Daily Reflection",
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="journal_tags",
 *                     type="array",
 *                     description="Array of tag IDs or tag names to associate with the journal",
 *                     @OA\Items(
 *                         oneOf={
 *                             @OA\Schema(type="integer", example=1, description="Existing tag ID"),
 *                             @OA\Schema(type="string", example="motivation", description="New tag name to be created")
 *                         }
 *                     ),
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="make_journal_private",
 *                     type="integer",
 *                     enum={0, 1},
 *                     description="Privacy setting: 0 for public, 1 for private",
 *                     example=0,
 *                     nullable=true
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Video request saved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the save operation was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="",
 *                 description="Success message (typically empty)"
 *             ),
 *             @OA\Property(
 *                 property="results",
 *                 type="object",
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="string",
 *                     example="123",
 *                     description="ID of the updated video request (returned as string)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Video request not found",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Video request not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The given data was invalid."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The request id field is required."
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Unauthenticated."
 *             )
 *         )
 *     )
 * )
 * 
  * @OA\Get(
 *     path="/v2/insights-v2",
 *     summary="Get emotional insights data for the authenticated user",
 *     description="Retrieves comprehensive emotional insights and analytics for the authenticated user based on their video journal submissions. Returns emotional parameters with current values, averages, time-based data, and progress tracking over various time periods.",
 *     tags={"Emotional Insights"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Insights data retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="success",
 *                 description="Response status"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Insights data retrieved successfully",
 *                 description="Success message"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="emotions",
 *                     type="object",
 *                     @OA\Property(
 *                         property="lastMeasured",
 *                         type="string",
 *                         example="Oct 15, 2025",
 *                         description="Date when emotions were last measured"
 *                     ),
 *                     @OA\Property(
 *                         property="profile",
 *                         type="array",
 *                         description="Array of emotional insights data",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="id", type="string", example="happiness", description="Emotion parameter ID"),
 *                             @OA\Property(property="emoji", type="string", example="😊", description="Emoji representation"),
 *                             @OA\Property(property="name", type="string", example="Happiness", description="Display name of emotion"),
 *                             @OA\Property(property="current", type="integer", example=75, description="Current emotion score"),
 *                             @OA\Property(property="average", type="integer", example=68, description="Average emotion score over time"),
 *                             @OA\Property(property="lastMeasured", type="string", example="Oct 15, 2025 2:30PM", description="Detailed timestamp of last measurement"),
 *                             @OA\Property(property="range", type="string", example="Above Normal", description="Performance range classification"),
 *                             @OA\Property(
 *                                 property="dayChartData",
 *                                 type="array",
 *                                 description="Weekly data for chart visualization",
 *                                 @OA\Items(
 *                                     type="object",
 *                                     @OA\Property(property="day", type="string", example="Mon"),
 *                                     @OA\Property(property="value", type="integer", example=72)
 *                                 )
 *                             ),
 *                             @OA\Property(
 *                                 property="timeChartData",
 *                                 type="object",
 *                                 description="Time of day averages",
 *                                 @OA\Property(property="morning", type="integer", example=70),
 *                                 @OA\Property(property="afternoon", type="integer", example=75),
 *                                 @OA\Property(property="evening", type="integer", example=68)
 *                             ),
 *                             @OA\Property(
 *                                 property="timelineData",
 *                                 type="object",
 *                                 description="Historical timeline data",
 *                                 @OA\Property(
 *                                     property="30days",
 *                                     type="array",
 *                                     description="Last 30 days data",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="date", type="string", example="2025-10-01"),
 *                                         @OA\Property(property="value", type="integer", example=72)
 *                                     )
 *                                 ),
 *                                 @OA\Property(
 *                                     property="3months",
 *                                     type="array",
 *                                     description="Last 3 months data",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="month", type="string", example="August 2025"),
 *                                         @OA\Property(property="value", type="integer", example=70)
 *                                     )
 *                                 ),
 *                                 @OA\Property(
 *                                     property="6months",
 *                                     type="array",
 *                                     description="Last 6 months data",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="month", type="string", example="May 2025"),
 *                                         @OA\Property(property="value", type="integer", example=65)
 *                                     )
 *                                 ),
 *                                 @OA\Property(
 *                                     property="all",
 *                                     type="array",
 *                                     description="All available data since start",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="month", type="string", example="January 2025"),
 *                                         @OA\Property(property="value", type="integer", example=60)
 *                                     )
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found or no insights data available",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="user not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/insights-v2/secondaryMetrics",
 *     summary="Get secondary/advanced emotional metrics for the authenticated user",
 *     description="Retrieves advanced emotional insights and secondary metrics for the authenticated user. Provides detailed analysis including self-honesty, stress recovery, cognitive balance, and anger metrics with enhanced status information and descriptions.",
 *     tags={"Emotional Insights"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Secondary metrics data retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="success",
 *                 description="Response status"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Insights data retrieved successfully",
 *                 description="Success message"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="advanced",
 *                     type="object",
 *                     @OA\Property(
 *                         property="lastMeasured",
 *                         type="string",
 *                         example="Oct 15, 2025",
 *                         description="Date when metrics were last measured"
 *                     ),
 *                     @OA\Property(
 *                         property="profile",
 *                         type="array",
 *                         description="Array of advanced metric insights",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="id", type="string", example="self_honesty", description="Metric parameter ID"),
 *                             @OA\Property(property="emoji", type="string", example="🔍", description="Emoji representation"),
 *                             @OA\Property(property="name", type="string", example="Self Honesty", description="Display name of metric"),
 *                             @OA\Property(property="current", type="integer", example=82, description="Current metric score"),
 *                             @OA\Property(property="average", type="integer", example=78, description="Average metric score over time"),
 *                             @OA\Property(property="lastMeasured", type="string", example="Oct 15, 2025 2:30PM", description="Detailed timestamp of last measurement"),
 *                             @OA\Property(property="range", type="string", example="Above Normal", description="Performance range classification"),
 *                             @OA\Property(property="description", type="string", example="Measures your ability to be honest with yourself about your emotions and thoughts.", description="Detailed description of the metric"),
 *                             @OA\Property(property="status", type="string", example="Above Normal", description="Current status classification"),
 *                             @OA\Property(property="statusMessage", type="string", example="You're showing good self-awareness and emotional honesty.", description="Interpretive message about current status"),
 *                             @OA\Property(
 *                                 property="statusType",
 *                                 type="string",
 *                                 enum={"Poor", "Good", "Great"},
 *                                 example="Good",
 *                                 description="Simplified status type for UI display"
 *                             ),
 *                             @OA\Property(
 *                                 property="dayChartData",
 *                                 type="array",
 *                                 description="Weekly data for chart visualization",
 *                                 @OA\Items(
 *                                     type="object",
 *                                     @OA\Property(property="day", type="string", example="Mon"),
 *                                     @OA\Property(property="value", type="integer", example=80)
 *                                 )
 *                             ),
 *                             @OA\Property(
 *                                 property="timeChartData",
 *                                 type="object",
 *                                 description="Time of day averages",
 *                                 @OA\Property(property="morning", type="integer", example=79),
 *                                 @OA\Property(property="afternoon", type="integer", example=82),
 *                                 @OA\Property(property="evening", type="integer", example=76)
 *                             ),
 *                             @OA\Property(
 *                                 property="timelineData",
 *                                 type="object",
 *                                 description="Historical timeline data with same structure as emotions endpoint",
 *                                 @OA\Property(property="30days", type="array", @OA\Items(type="object")),
 *                                 @OA\Property(property="3months", type="array", @OA\Items(type="object")),
 *                                 @OA\Property(property="6months", type="array", @OA\Items(type="object")),
 *                                 @OA\Property(property="all", type="array", @OA\Items(type="object"))
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found or no metrics data available",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="user not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 * 
  * @OA\Get(
 *     path="/v2/insights-v2/vijos",
 *     summary="Get credibility score insights for the authenticated user",
 *     description="Retrieves comprehensive credibility score data and analytics for the authenticated user across different video journal categories (Vijos). Returns current scores, averages, time-based data, and progress tracking over various periods with detailed metrics aggregation.",
 *     tags={"Credibility Score Insights"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Credibility score insights retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="success",
 *                 description="Response status"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Insights data retrieved successfully",
 *                 description="Success message"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="vijos",
 *                     type="object",
 *                     @OA\Property(
 *                         property="lastMeasured",
 *                         type="string",
 *                         example="Oct 15, 2025",
 *                         description="Date when credibility scores were last measured"
 *                     ),
 *                     @OA\Property(
 *                         property="profile",
 *                         type="array",
 *                         description="Array of credibility score insights for different catalog types",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=1, description="Catalog ID"),
 *                             @OA\Property(property="emoji", type="string", example="💼", description="Catalog emoji representation"),
 *                             @OA\Property(property="name", type="string", example="Work Performance", description="Catalog/Vijo type name"),
 *                             @OA\Property(property="video_type_id", type="integer", example=1, description="Associated video type ID"),
 *                             @OA\Property(property="current", type="integer", example=78, description="Current credibility score (0-100)"),
 *                             @OA\Property(property="average", type="integer", example=72, description="Average credibility score over time"),
 *                             @OA\Property(property="lastMeasured", type="string", example="Oct 15, 2025 3:45PM", description="Detailed timestamp of last measurement"),
 *                             @OA\Property(
 *                                 property="range",
 *                                 type="string",
 *                                 enum={"Below Normal", "Normal", "Above Normal"},
 *                                 example="Above Normal",
 *                                 description="Performance range classification based on standard deviation"
 *                             ),
 *                             @OA\Property(
 *                                 property="dayChartData",
 *                                 type="array",
 *                                 description="Weekly credibility score data for chart visualization",
 *                                 @OA\Items(
 *                                     type="object",
 *                                     @OA\Property(property="day", type="string", example="Mon", description="Day of the week"),
 *                                     @OA\Property(property="value", type="integer", example=75, description="Credibility score for that day")
 *                                 )
 *                             ),
 *                             @OA\Property(
 *                                 property="timeChartData",
 *                                 type="object",
 *                                 description="Credibility score averages by time of day",
 *                                 @OA\Property(property="morning", type="integer", example=76, description="Morning average score"),
 *                                 @OA\Property(property="afternoon", type="integer", example=78, description="Afternoon average score"),
 *                                 @OA\Property(property="evening", type="integer", example=74, description="Evening average score")
 *                             ),
 *                             @OA\Property(
 *                                 property="timelineData",
 *                                 type="object",
 *                                 description="Historical credibility score timeline data",
 *                                 @OA\Property(
 *                                     property="30days",
 *                                     type="array",
 *                                     description="Last 30 days credibility score progression",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="date", type="string", format="date", example="2025-10-01"),
 *                                         @OA\Property(property="value", type="integer", example=74, description="Credibility score")
 *                                     )
 *                                 ),
 *                                 @OA\Property(
 *                                     property="3months",
 *                                     type="array",
 *                                     description="Last 3 months aggregated data",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="month", type="string", example="August 2025"),
 *                                         @OA\Property(property="value", type="integer", example=72, description="Monthly average credibility score")
 *                                     )
 *                                 ),
 *                                 @OA\Property(
 *                                     property="6months",
 *                                     type="array",
 *                                     description="Last 6 months aggregated data",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="month", type="string", example="May 2025"),
 *                                         @OA\Property(property="value", type="integer", example=68, description="Monthly average credibility score")
 *                                     )
 *                                 ),
 *                                 @OA\Property(
 *                                     property="all",
 *                                     type="array",
 *                                     description="All historical data since user started",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="month", type="string", example="January 2025"),
 *                                         @OA\Property(property="value", type="integer", example=65, description="Monthly average credibility score")
 *                                     )
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found or no credibility score data available",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="user not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/v2/start-video-request",
 *     summary="Start a new video request or retrieve existing request details",
 *     description="Initiates a video recording process by either creating a new video request or retrieving details for an existing one. Returns catalog information, recording constraints, questions for KPIs, and suggested next catalogs. Supports both new recordings and responding to shared requests.",
 *     tags={"Video Requests"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="catalog_id",
 *                     type="integer",
 *                     description="ID of the catalog to start recording (required if request_id not provided)",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="integer",
 *                     description="ID of existing video request to continue/respond to (optional)",
 *                     example=123
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Video request started successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the request was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="",
 *                 description="Response message (typically empty on success)"
 *             ),
 *             @OA\Property(
 *                 property="results",
 *                 type="object",
 *                 @OA\Property(
 *                     property="parent_catalog_id",
 *                     type="string",
 *                     example="0",
 *                     description="ID of parent catalog if this is a sub-catalog"
 *                 ),
 *                 @OA\Property(
 *                     property="catalog_id",
 *                     type="string",
 *                     example="1",
 *                     description="ID of the catalog being used for recording"
 *                 ),
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="integer",
 *                     example=123,
 *                     description="ID of the video request (newly created or existing)"
 *                 ),
 *                 @OA\Property(
 *                     property="record_date",
 *                     type="string",
 *                     format="date",
 *                     example="2025-10-15",
 *                     description="Date when the recording should be made"
 *                 ),
 *                 @OA\Property(
 *                     property="video_types",
 *                     type="object",
 *                     description="Video type metrics and KPI information",
 *                     @OA\Property(property="metrics", type="integer", example=6, description="Total number of metrics for this video type"),
 *                     @OA\Property(property="kpis", type="integer", example=2, description="Number of KPIs for this video type"),
 *                     @OA\Property(property="kpi_metrics", type="integer", example=3, description="Number of KPI-related questions available")
 *                 ),
 *                 @OA\Property(
 *                     property="video_type_id",
 *                     type="integer",
 *                     nullable=true,
 *                     example=1,
 *                     description="ID of the associated video type"
 *                 ),
 *                 @OA\Property(
 *                     property="min_record_time",
 *                     type="string",
 *                     example="15",
 *                     description="Minimum recording time in seconds"
 *                 ),
 *                 @OA\Property(
 *                     property="record_time",
 *                     type="string",
 *                     example="60",
 *                     description="Maximum recording time in seconds"
 *                 ),
 *                 @OA\Property(
 *                     property="questions",
 *                     type="array",
 *                     description="Array of KPI-related questions for this catalog",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1, description="Question/metric specification ID"),
 *                         @OA\Property(property="name", type="string", example="Stress Level", description="Name of the metric"),
 *                         @OA\Property(property="question", type="string", example="How stressed do you feel right now?", description="Question text for user input"),
 *                         @OA\Property(property="video_question", type="string", example="Describe your current stress level in detail.", description="Question for video recording"),
 *                         @OA\Property(property="range", type="string", example="1-10", description="Expected range for answers")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="userTags",
 *                     type="array",
 *                     description="User's available tags for this category",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Work"),
 *                         @OA\Property(property="category_id", type="integer", example=1)
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="next_vijo",
 *                     type="array",
 *                     description="Suggested catalogs for next recording session",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="catalog_id", type="string", example="2"),
 *                         @OA\Property(property="title", type="string", example="Evening Reflection"),
 *                         @OA\Property(property="description", type="string", example="Reflect on your day"),
 *                         @OA\Property(property="catalogEmoji", type="string", example="🌅"),
 *                         @OA\Property(property="category_name", type="string", example="Wellness"),
 *                         @OA\Property(property="isPremium", type="string", example="0"),
 *                         @OA\Property(property="video_type_id", type="string", example="1")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing required catalog_id",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Catalog ID is required."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized access to video request",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Unauthorized access to this request."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Catalog not found",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Catalog not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Unauthenticated."
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/v2/record-video-request",
 *     summary="Submit catalog answers and video recording data",
 *     description="Submits answers to catalog questions, KPI metrics, and optional video thumbnail after completing a video journal recording. This endpoint processes the user's responses, stores metrics data, handles video thumbnail upload, and creates the catalog answer record.",
 *     tags={"Video Recording"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"request_id", "catalog_id"},
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="integer",
 *                     description="ID of the video request being processed",
 *                     example=123
 *                 ),
 *                 @OA\Property(
 *                     property="catalog_id",
 *                     type="integer",
 *                     description="ID of the catalog used for recording",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="question1_score",
 *                     type="number",
 *                     description="Score for KPI question 1 (required if applicable)",
 *                     example=8.5,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="question2_score",
 *                     type="number",
 *                     description="Score for KPI question 2 (required if applicable)",
 *                     example=7.2,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="question3_score",
 *                     type="number",
 *                     description="Score for KPI question 3 (required if applicable)",
 *                     example=9.0,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="cred_score",
 *                     type="number",
 *                     description="Overall credibility score for the recording",
 *                     example=78.5,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric1_answer",
 *                     type="string",
 *                     maxLength=50,
 *                     description="Answer text for metric 1",
 *                     example="Confident",
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric1Range",
 *                     type="number",
 *                     description="Range value for metric 1",
 *                     example=8.0,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric1Significance",
 *                     type="integer",
 *                     description="Significance level for metric 1",
 *                     example=3,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric2_answer",
 *                     type="string",
 *                     maxLength=50,
 *                     description="Answer text for metric 2",
 *                     example="Motivated",
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric2Range",
 *                     type="number",
 *                     description="Range value for metric 2",
 *                     example=7.5,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric2Significance",
 *                     type="integer",
 *                     description="Significance level for metric 2",
 *                     example=4,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric3_answer",
 *                     type="string",
 *                     maxLength=50,
 *                     description="Answer text for metric 3",
 *                     example="Focused",
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric3Range",
 *                     type="number",
 *                     description="Range value for metric 3",
 *                     example=9.0,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="metric3Significance",
 *                     type="integer",
 *                     description="Significance level for metric 3",
 *                     example=5,
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="n8n_executionId",
 *                     type="string",
 *                     maxLength=50,
 *                     description="N8N workflow execution ID for tracking",
 *                     example="exec_123456789",
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="video_thumbnail_file",
 *                     type="string",
 *                     format="binary",
 *                     description="Video thumbnail image file (JPEG, PNG, etc.)",
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="record_date",
 *                     type="string",
 *                     format="date-time",
 *                     description="Recording date (defaults to current timestamp if not provided)",
 *                     example="2025-10-15T14:30:00Z",
 *                     nullable=true
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Catalog answer created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true,
 *                 description="Indicates if the submission was successful"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Catalog answer created successfully.",
 *                 description="Success confirmation message"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="integer",
 *                     example=123,
 *                     description="ID of the processed video request"
 *                 ),
 *                 @OA\Property(
 *                     property="record_category",
 *                     type="integer",
 *                     example=0,
 *                     description="Recording category classification"
 *                 ),
 *                 @OA\Property(
 *                     property="record_date",
 *                     type="string",
 *                     format="date-time",
 *                     example="2025-10-15T14:30:00.000000Z",
 *                     description="Date and time when the recording was processed"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing required KPI question scores",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Missing required question: question1_score"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Credibility score configuration not found for catalog",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Cred score not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The given data was invalid."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="request_id",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         oneOf={
 *                             @OA\Schema(type="string", example="The request id field is required."),
 *                             @OA\Schema(type="string", example="The selected request id is invalid.")
 *                         }
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="catalog_id",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         oneOf={
 *                             @OA\Schema(type="string", example="The catalog id field is required."),
 *                             @OA\Schema(type="string", example="The selected catalog id is invalid.")
 *                         }
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="cred_score",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The cred score must be a number."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="metric1_answer",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The metric1 answer may not be greater than 50 characters."
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="video_thumbnail_file",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="The video thumbnail file must be a file."
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error during processing",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Internal server error."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Unauthenticated."
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/v2/catalogs-by-category/{categoryId}",
 *     summary="Get catalogs by category ID",
 *     description="Retrieves all active catalogs (non-deleted) that belong to a specific category. Returns a list of catalogs with their basic information including title, description, recording time limits, and other metadata.",
 *     tags={"Catalogs"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="categoryId",
 *         in="path",
 *         required=true,
 *         description="ID of the category to filter catalogs by",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Catalogs by category retrieved successfully (array may be empty if none found)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Catalogs by category retrieved successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 description="Array of catalogs in the specified category (empty if none found)",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1, description="Catalog ID"),
 *                     @OA\Property(property="title", type="string", example="Daily Reflection", description="Catalog title"),
 *                     @OA\Property(property="description", type="string", example="A daily reflection on your thoughts and feelings", description="Catalog description", nullable=true),
 *                     @OA\Property(property="tags", type="string", example="reflection,mindfulness,daily", description="Comma-separated tags", nullable=true),
 *                     @OA\Property(property="min_record_time", type="integer", example=30, description="Minimum recording time in seconds"),
 *                     @OA\Property(property="max_record_time", type="integer", example=300, description="Maximum recording time in seconds"),
 *                     @OA\Property(property="emoji", type="string", example="🤔", description="Emoji representation of the catalog", nullable=true),
 *                     @OA\Property(property="status", type="integer", enum={0, 1, 2, 3}, example=1, description="Catalog status: 0=inactive, 1=active, 2=draft, 3=archived"),
 *                     @OA\Property(property="parent_catalog_id", type="integer", example=null, description="Parent catalog ID if this is a sub-catalog", nullable=true),
 *                     @OA\Property(property="category_id", type="integer", example=1, description="Category ID this catalog belongs to"),
 *                     @OA\Property(property="is_promotional", type="boolean", example=false, description="Whether this is a promotional catalog"),
 *                     @OA\Property(property="is_premium", type="boolean", example=false, description="Whether this requires premium subscription"),
 *                     @OA\Property(property="is_deleted", type="integer", example=0, description="Soft delete flag (0=active, 1=deleted)"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-15T10:00:00.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-15T10:00:00.000000Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 */
class SwaggerAnnotations extends Controller
{
}