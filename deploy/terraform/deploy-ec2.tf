terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.16"
    }
  }

  required_version = ">= 1.2.0"
}

provider "aws" {
  region  = "ap-northeast-1"
}

# resource "aws_instance" "app_server" {
#   ami           = "ami-030cf0a1edb8636ab"
#   instance_type = "t2.micro"

#   tags = {
#     Name = "ExampleAppServerInstance"
#   }
# }

resource "aws_vpc" "default" {
  cidr_block           = "10.0.0.0/16"
  enable_dns_hostnames = true
}

resource "aws_internet_gateway" "gw" {
  vpc_id = aws_vpc.default.id
}

resource "aws_subnet" "tf_test_subnet" {
  vpc_id                  = aws_vpc.default.id
  cidr_block              = "10.0.0.0/24"
  map_public_ip_on_launch = true

  depends_on = [aws_internet_gateway.gw]
}

resource "aws_security_group" "instance" {
  name = "sec_open_80"
  vpc_id = aws_vpc.default.id
  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

resource "aws_instance" "foo" {
  ami           = "ami-030cf0a1edb8636ab"
  instance_type = "t2.micro"

  private_ip = "10.0.0.12"
  subnet_id  = aws_subnet.tf_test_subnet.id
  vpc_security_group_ids = [aws_security_group.instance.id]

  user_data = file("script.bash") 
}

resource "aws_eip" "bar" {
  vpc = true

  instance                  = aws_instance.foo.id
  associate_with_private_ip = "10.0.0.12"
  depends_on                = [aws_internet_gateway.gw]
}

resource "aws_route53_record" "dev-ns" {
  zone_id = "Z10472882NG7D5B02AX8W"
  name    = "thanhpn.online"
  type    = "A"
  ttl     = "30"
  records = [aws_eip.bar.public_ip]
}
