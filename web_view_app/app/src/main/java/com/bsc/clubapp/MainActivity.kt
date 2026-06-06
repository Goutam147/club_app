package com.bsc.clubapp

import android.content.Context
import android.content.Intent
import android.net.ConnectivityManager
import android.net.NetworkCapabilities
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.webkit.*
import android.widget.Button
import android.widget.ProgressBar
import android.widget.Toast
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var progressBar: ProgressBar
    private lateinit var swipeRefreshLayout: SwipeRefreshLayout
    private lateinit var layoutOffline: View
    private lateinit var btnRetry: Button

    private val baseUrl = "https://club.goutam.fun/"
    private var filePathCallback: ValueCallback<Array<Uri>>? = null

    // Register modern Activity Result API for handling file choose requests from WebView
    private val fileChooserLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { result ->
        if (result.resultCode == RESULT_OK) {
            val data = result.data
            val uris = if (data != null) {
                // Check if multiple files are selected
                val clipData = data.clipData
                if (clipData != null) {
                    val uriList = ArrayList<Uri>()
                    for (i in 0 until clipData.itemCount) {
                        uriList.add(clipData.getItemAt(i).uri)
                    }
                    uriList.toTypedArray()
                } else {
                    data.data?.let { arrayOf(it) } ?: emptyArray()
                }
            } else {
                emptyArray()
            }
            filePathCallback?.onReceiveValue(if (uris.isNotEmpty()) uris else null)
        } else {
            filePathCallback?.onReceiveValue(null)
        }
        filePathCallback = null
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        // Switch from splash theme to application theme before drawing views
        setTheme(R.style.Theme_BSC)
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        // Initialize views
        webView = findViewById(R.id.webView)
        progressBar = findViewById(R.id.progressBar)
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout)
        layoutOffline = findViewById(R.id.layoutOffline)
        btnRetry = findViewById(R.id.btnRetry)

        setupWebView()
        setupListeners()
        setupBackNavigation()

        // Load the website or show offline view
        loadUrl(baseUrl)
    }

    private fun setupWebView() {
        webView.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            databaseEnabled = true
            useWideViewPort = true
            loadWithOverviewMode = true
            cacheMode = WebSettings.LOAD_DEFAULT
            
            // Set custom user agent to identify requests coming from Android app
            val defaultUserAgent = userAgentString
            userAgentString = "$defaultUserAgent (BSC Android App)"
        }

        // Enable cookie persistence for session management
        CookieManager.getInstance().apply {
            setAcceptCookie(true)
            setAcceptThirdPartyCookies(webView, true)
        }

        // Setup clients
        webView.webViewClient = object : WebViewClient() {
            override fun onPageStarted(view: WebView?, url: String?, favicon: android.graphics.Bitmap?) {
                super.onPageStarted(view, url, favicon)
                progressBar.visibility = View.VISIBLE
                progressBar.progress = 0
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                super.onPageFinished(view, url)
                progressBar.visibility = View.GONE
                swipeRefreshLayout.isRefreshing = false
            }

            @Deprecated("Deprecated in Java")
            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                if (url != null && (url.startsWith("http://") || url.startsWith("https://"))) {
                    // Open in webview for our domain
                    if (url.contains("club.goutam.fun")) {
                        return false
                    }
                    // For external links, open in the browser
                    val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                    startActivity(intent)
                    return true
                }
                // Handle non-web protocols like tel:, mailto:, sms:, etc.
                try {
                    val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                    startActivity(intent)
                    return true
                } catch (e: Exception) {
                    return false
                }
            }

            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                super.onReceivedError(view, request, error)
                // Only show offline layout if the main URL fails to load
                if (request?.isForMainFrame == true) {
                    showOfflineView()
                }
            }
        }

        webView.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                super.onProgressChanged(view, newProgress)
                progressBar.progress = newProgress
                if (newProgress >= 100) {
                    progressBar.visibility = View.GONE
                } else {
                    progressBar.visibility = View.VISIBLE
                }
            }

            // Handle file uploads (e.g. receipt files, profile pictures)
            override fun onShowFileChooser(
                webView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                this@MainActivity.filePathCallback?.onReceiveValue(null)
                this@MainActivity.filePathCallback = filePathCallback

                val intent = fileChooserParams?.createIntent() ?: Intent(Intent.ACTION_GET_CONTENT).apply {
                    type = "*/*"
                    addCategory(Intent.CATEGORY_OPENABLE)
                }
                try {
                    fileChooserLauncher.launch(intent)
                } catch (e: Exception) {
                    this@MainActivity.filePathCallback = null
                    return false
                }
                return true
            }
        }
    }

    private fun setupListeners() {
        swipeRefreshLayout.setOnRefreshListener {
            if (isNetworkAvailable()) {
                webView.reload()
            } else {
                swipeRefreshLayout.isRefreshing = false
                showOfflineView()
            }
        }

        btnRetry.setOnClickListener {
            if (isNetworkAvailable()) {
                hideOfflineView()
                webView.reload()
            } else {
                Toast.makeText(this, "Still offline. Please check your connection.", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun setupBackNavigation() {
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (webView.canGoBack()) {
                    webView.goBack()
                } else {
                    finish()
                }
            }
        })
    }

    private fun loadUrl(url: String) {
        if (isNetworkAvailable()) {
            hideOfflineView()
            webView.loadUrl(url)
        } else {
            showOfflineView()
        }
    }

    private fun showOfflineView() {
        webView.visibility = View.GONE
        swipeRefreshLayout.visibility = View.GONE
        layoutOffline.visibility = View.VISIBLE
        swipeRefreshLayout.isRefreshing = false
    }

    private fun hideOfflineView() {
        layoutOffline.visibility = View.GONE
        webView.visibility = View.VISIBLE
        swipeRefreshLayout.visibility = View.VISIBLE
    }

    private fun isNetworkAvailable(): Boolean {
        val connectivityManager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        val activeNetwork = connectivityManager.activeNetwork ?: return false
        val capabilities = connectivityManager.getNetworkCapabilities(activeNetwork) ?: return false
        return capabilities.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET)
    }
}
