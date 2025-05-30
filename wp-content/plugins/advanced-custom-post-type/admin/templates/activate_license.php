<?php

use ACPT\Utils\PHP\IP;

$referer = json_encode([
	'site' => site_url(),
	'siteName' => get_bloginfo('name'),
	'ip' => IP::getClientIP(),
]);

$link = "https://acpt.io/activation?referer=".base64_encode($referer);
?>
<div class="acpt-container">
    <div class="acpt-main" style="
        display: flex;
        min-height: 100vh;
        justify-content: center;
        align-content: center;
        align-items: center;"
    >
        <div class="acpt-main-wrapper">
            <div class="w-100 justify-center mb-24 i-flex-center s-4">
                <svg width="52" height="52" viewBox="0 0 637 695" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M302.676 417.485C302.676 381.758 283.616 348.746 252.676 330.882L30 202.32C16.6667 194.622 0 204.245 0 219.641V476.765C0 512.491 19.0599 545.504 50 563.367L272.676 691.929C286.009 699.627 302.676 690.005 302.676 674.609V417.485ZM237.676 596.667V417.485C237.676 404.981 231.005 393.426 220.176 387.174L65 297.583V476.765C65 489.269 71.671 500.824 82.5 507.076L237.676 596.667Z" fill="url(#paint0_linear_2042_20)"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M383.338 330.882C352.398 348.746 333.338 381.758 333.338 417.485L333.338 674.609C333.338 690.005 350.005 699.627 363.338 691.929L586.014 563.367C616.954 545.504 636.014 512.491 636.014 476.765L636.014 219.641C636.014 204.245 619.347 194.622 606.014 202.32L383.338 330.882ZM571.014 297.583L415.838 387.174C405.009 393.426 398.338 404.981 398.338 417.485L398.338 596.667L553.514 507.076C564.343 500.824 571.014 489.269 571.014 476.765L571.014 297.583Z" fill="#02C39A"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M367.676 14.2424C336.736 -3.62085 298.616 -3.62085 267.676 14.2424L45 142.804C31.6667 150.502 31.6667 169.747 45 177.445L267.676 306.007C298.616 323.871 336.736 323.871 367.676 306.007L590.352 177.445C603.685 169.747 603.685 150.502 590.352 142.804L367.676 14.2424ZM490.352 160.125L335.176 70.5341C324.347 64.2819 311.005 64.2819 300.176 70.5341L145 160.125L300.176 249.716C311.005 255.968 324.347 255.968 335.176 249.716L490.352 160.125Z" fill="#02C39A"/>
                    <defs>
                        <linearGradient id="paint0_linear_2042_20" x1="303" y1="709" x2="10" y2="204" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#C9FF57"/>
                            <stop offset="1" stop-color="#80E0CC"/>
                        </linearGradient>
                    </defs>
                </svg>
                <svg class="hidden-xs" width="99" height="29" viewBox="0 0 458 123" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M457.998 1.95825V25.3853H426.192V122H396.951V25.3853H365.145V1.95825H457.998Z" fill="#111"/>
                    <path d="M356.42 40.6043C356.42 47.5583 354.824 53.9423 351.632 59.7563C348.44 65.4563 343.538 70.0733 336.926 73.6073C330.314 77.1413 322.106 78.9083 312.302 78.9083H294.176V122H264.935V1.95825H312.302C321.878 1.95825 329.972 3.61125 336.584 6.91725C343.196 10.2232 348.155 14.7833 351.461 20.5973C354.767 26.4113 356.42 33.0803 356.42 40.6043ZM310.079 55.6523C315.665 55.6523 319.826 54.3413 322.562 51.7193C325.298 49.0973 326.666 45.3923 326.666 40.6043C326.666 35.8163 325.298 32.1113 322.562 29.4893C319.826 26.8673 315.665 25.5563 310.079 25.5563H294.176V55.6523H310.079Z" fill="#111"/>
                    <path d="M129.722 61.8083C129.722 49.9523 132.287 39.4073 137.417 30.1733C142.547 20.8253 149.672 13.5863 158.792 8.45633C168.026 3.21233 178.457 0.590332 190.085 0.590332C204.335 0.590332 216.533 4.35233 226.679 11.8763C236.825 19.4003 243.608 29.6603 247.028 42.6563H214.88C212.486 37.6403 209.066 33.8213 204.62 31.1993C200.288 28.5773 195.329 27.2663 189.743 27.2663C180.737 27.2663 173.441 30.4013 167.855 36.6713C162.269 42.9413 159.476 51.3203 159.476 61.8083C159.476 72.2963 162.269 80.6753 167.855 86.9453C173.441 93.2153 180.737 96.3503 189.743 96.3503C195.329 96.3503 200.288 95.0393 204.62 92.4173C209.066 89.7953 212.486 85.9763 214.88 80.9603H247.028C243.608 93.9563 236.825 104.216 226.679 111.74C216.533 119.15 204.335 122.855 190.085 122.855C178.457 122.855 168.026 120.29 158.792 115.16C149.672 109.916 142.547 102.677 137.417 93.4433C132.287 84.2093 129.722 73.6643 129.722 61.8083Z" fill="#111"/>
                    <path d="M83.3291 100.796H38.5271L31.3451 122H0.736084L44.1701 1.95825H78.0281L121.462 122H90.5111L83.3291 100.796ZM75.8051 78.2243L60.9281 34.2773L46.2221 78.2243H75.8051Z" fill="#02C39A"/>
                </svg>
            </div>
            <div class="max-w-90pc m-auto">
                <div class="p-24 with-shadow bg-white flex-column s-24">
                    <h1><?php echo __('Activate your license', ACPT_PLUGIN_NAME); ?></h1>
                    <?php if(isset($_GET['error'])): ?>
                        <p class="notice notice-error is-dismissible">
                            <?php echo urldecode($_GET['error']); ?>
                        </p>
                    <?php endif; ?>
                    <div>
                        Select your preferred method from the <strong>two available</strong> to activate the plugin.
                    </div>
                    <div class="acpt-alert acpt-alert-warning">
                        <strong>WARNING</strong>: To activate the plugin you need an Internet connection and <a href="https://www.php.net/manual/en/book.curl.php" target="_blank">cURL</a> enabled on your website.
                    </div>
                    <div class="container">
                        <div class="col-6 with-border b-rounded">
                            <div class="p-24 flex-column s-12">
                                <h2>Option 1</h2>
                                <div>
                                    Go directly to the <a href="<?php echo $link; ?>">ACPT activation page</a> and activate your license.
                                </div>
                                <div>
                                    Please note that <strong>your ACPT user email</strong> is also needed.
                                </div>
                                <div>
                                    <a href="<?php echo $link; ?>" class="acpt-btn acpt-btn-primary">
                                        <?php echo __('Activate your license now', ACPT_PLUGIN_NAME); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 with-border b-rounded">
                            <div class="p-24 flex-column s-12">
                                <h2>Option 2</h2>
                                <div>Inside your <pre class="color-danger m-0 i-flex-center">wp-config.php</pre> file, add those two lines:</div>
                                <code style="background: #f8f8f8 !important;" class="b-rounded bg-pale-gray p-12">
                                    define(<span class="color-info">'ACPT_LICENSE_KEY'</span>, <span class="color-primary">'xxxxxxxx'</span>);<br>
                                    define(<span class="color-info">'ACPT_LICENSE_EMAIL'</span>, <span class="color-primary">'name@site.com'</span>);
                                </code>
                                <div>
                                    Replace with the correct values and refresh this page, the plugin will automatically activate.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-24 w-100 justify-center text-center flex s-12">
                    <a href="https://docs.acpt.io" target="_blank">
                        Documentation
                    </a>
                    <a href="https://dash.acpt.io/" target="_blank">
                        My account
                    </a>
                    <a href="https://acpt.io/changelog/" target="_blank">
                        Changelog
                    </a>
                    <a href="https://www.facebook.com/groups/880817719861018" target="_blank">
                        Facebook group
                    </a>
                </div>
                <footer class="mt-24 text-center">
                    Copyright &copy; 2021 - <?php echo date("Y"); ?>
                    <a href="https://acpt.io" target="_blank">ACPT</a>
                </footer>
            </div>
        </div>
    </div>
</div>