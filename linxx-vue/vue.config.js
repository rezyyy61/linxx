const { defineConfig } = require('@vue/cli-service')
const webpack = require('webpack')

module.exports = defineConfig({
    transpileDependencies: true,

    devServer: {
        port: 8081,
        host: '0.0.0.0',
        open: true,
        proxy: {
            '/api': {
                target: 'http://192.168.1.193:8080',
                changeOrigin: true
            },
            '/sanctum': {
                target: 'http://192.168.1.193:8080',
                changeOrigin: true
            }
        }

    },

    configureWebpack: {
        plugins: [
            new webpack.DefinePlugin({
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false)
            })
        ]
    }
})
