pipeline {

  environment {
    registry = "localhost:5000/luclecocq/api"
    dockerImage = ""
  }
  
  agent any
  
  stages {

    stage('Checkout Source') {
      steps {
        git 'https://github.com/PracticeEnterpriseTM2020/Api.git'
      }
    }
    
    stage('Build image') {
      steps{
        script {
          dockerImage = docker.build registry + ":$BUILD_NUMBER"
        }
      }
    }
    
    stage('Push Image') {
      steps{
        script {
          docker.withRegistry( "" ) {
            dockerImage.push()
          }
        }
      }
    }
    
    stage('Deploy App') {
      steps {
        script {
          kubernetesDeploy(configs: "api-php-deployment.yaml", kubeconfigId: "mykubeconfig")
          sh 'kubectl set image deployments/api-php api-php=localhost:5000/luclecocq/api:$BUILD_NUMBER'
          sh 'docker image prune -a --force'
        }
      }
    }

  }

}
