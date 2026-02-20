/**
 * Main Application JavaScript
 */
(function () {
    'use strict';

    const config = {
        animationDuration: 300,
        apiEndpoint: 'https://api.example.com/data',
        debugMode: false,
        maxRetries: 3,
        themes: {
            light: { backgroundColor: '#ffffff', textColor: '#333333' },
            dark: { backgroundColor: '#222222', textColor: '#f5f5f5' }
        }
    };

    const utils = {
        debounce: function (func, wait) {
            let timeout;
            return function () {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        },

        fetchData: function (url) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', url);
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        reject(new Error(xhr.statusText));
                    }
                };
                xhr.onerror = function () {
                    reject(new Error('Network Error'));
                };
                xhr.send();
            });
        },

        storage: {
            set: function (key, value) {
                try {
                    localStorage.setItem(key, JSON.stringify(value));
                    return true;
                } catch (e) {
                    console.error('Storage error:', e);
                    return false;
                }
            },
            get: function (key, defaultValue) {
                try {
                    const item = localStorage.getItem(key);
                    return item ? JSON.parse(item) : (defaultValue || null);
                } catch (e) {
                    return defaultValue || null;
                }
            }
        }
    };

    function init() {
        const savedSettings = utils.storage.get('settings');
        if (savedSettings) {
            Object.assign(config, savedSettings);
        }
        if (config.debugMode) {
            console.log('App initialized with config:', config);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
