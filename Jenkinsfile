pipeline {
  agent none
  stages {
    stage('build for master') {
    agent {label 'app_server'}
        when {
            branch 'master'
            }
        steps {
            sh "docker-compose build"
            sh "docker tag unilibrary_backend unilibrary_backend_prod:${env.BUILD_ID}"
            sh "docker tag nginx_backend nginx_backend_prod:${env.BUILD_ID}"
        }
    }
    stage('deploy for master') {
    agent {label 'app_server'}
      when {
        branch 'master'
        }
        steps {
        script {
            sh "docker service update --image unilibrary_backend_prod:${env.BUILD_ID} PROD_app"
            sh "docker service update --image nginx_backend_prod:${env.BUILD_ID} PROD_nginx"
        }
        }
    }

    stage('build for development') {
    agent {label 'dev_server'}
    when {
            branch 'dev'
            }
        steps {
            sh "docker-compose build"
            sh "docker tag unilibrary_backend unilibrary_backend_dev:${env.BUILD_ID}"
            sh "docker tag nginx_backend nginx_backend_dev:${env.BUILD_ID}"
        }
    }
    stage('deploy for development') {
    agent {label 'dev_server'}
      when {
        branch 'dev'
        }
        steps {
        script {
            sh "docker service update --image unilibrary_backend_dev:${env.BUILD_ID} DEV_app"
            sh "docker service update --image nginx_backend_dev:${env.BUILD_ID} DEV_nginx"
        }
        }
    }
  }
}
