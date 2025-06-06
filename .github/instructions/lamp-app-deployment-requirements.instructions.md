---
applyTo: '**'
---
Coding standards, domain knowledge, and preferences that AI should follow.

# LAMP App Deployment Requirements

1. Use AWS CLI for all commands to interact with AWS services.
2. Ensure all commands are executed in the context of the provided AWS account.
3. Use the latest version of AWS CLI.
4. Use the Visual Studio Code's integrated terminal for executing commands.
5. Ensure that all commands are well-documented with comments explaining their purpose.
6. Debug any issues that arise during the execution of commands.
7. Have to configure all the services step by step, if any issues arise, debug them and provide the solution then continue with the next step.


**Assignment Description:**

Consider a small startup currently in its early stages of operation. Their setup comprises
a LAMP stack (MySQL, Apache, and PHP) running on a single desktop PC in a small office.
Like many early-stage startups, it expects significant, rapid, and unpredictable growth in
the coming months.

They want to move their offering to Amazon Web Services (AWS). As part of moving their
current infrastructure to the cloud, they have requested a system architecture and
implementation on AWS that addresses the following concerns:

1. Scalability: The application must be able to scale on demand. Given the
    uncertainty around the timing and extent of future growth, the startup wants to
    avoid both over-provisioning and under-provisioning.
2. Disaster Recovery: The system must incorporate disaster recovery measures to
    maintain high performance and throughput and ensure continuous availability
    even under adverse conditions.

**Your task in this assignment**

Design and deploy a scalable, elastic, highly available, and fault-tolerant architecture
that supports the startup's organic growth. This design should explicitly address the
concerns outlined in the above project brief, ensuring it meets all specified
requirements.

**Assignment Deliverables:**
The deliverables of Assignment are two-fold as below:

```
(a) Deliverable 1: (AWS system architecture). Prepare a PDF document (limited to four
or five pages) that clearly and concisely presents your proposed architecture
diagram. Provide a justification for each Amazon Web Services (AWS) component
you include, explaining how it supports the solution's requirements. Additionally,
explicitly outline any assumptions made during the design process and list all
AWS services used to implement the solution. You may use any diagramming tool
of your choice to illustrate the system architecture.
```
```
(b) Deliverable 2: (Develop the Application in AWS). Use the AWS account that has
been provided to you to build and deploy the application. Leverage AWS Elastic Beanstalk 
to configure and deploy the Application.

```

Mandatory requirements to utilize the following AWS services:
(a) AWS Beanstalk
(b) Amazon EC2
(c) Custom AMI (Amazon Machine Image). (Please note that you are required
to create your own custom AMI)
(d) Custom Security groups allowing HTTP and SSH requests (All instances
must use the same custom security group)
(e) Load Balancer
(f) Auto Scaling (with a minimum of two instances and a maximum of eight
instances). Set scaling triggers on network output traffic with an upper
threshold of 60% and a lower threshold of 30%.
(g) RDS (multi-availability zones deployed)
(h) Custom Virtual Private Cloud (VPC) (with at least two subnets in different
Availability zones). All subnets must be public.
(i) All instances must use the same custom key pairs.
(j) Set email notifications for important events in your environment (if using
Elastic Beanstalk)


**Resources:**

1. https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/GettingStarted.html
2. https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/CHAP_Tutorials.WebServerDB.CreateWebServer.html
3. https://docs.aws.amazon.com/autoscaling/ec2/userguide/as-register-lbs-with-asg.html#as-register-lbs-console
4. https://docs.aws.amazon.com/AWSEC2/latest/UserGuide/install-LAMP.html


**Assessment Process for Assignment 3**

The following assessment criteria will be used in grading process for this assignment.

## 1. Criteria: System Architecture

```
- Does the developed system architecture meet the requirements outlined in the Assignment Description?
- Have relevant AWS services been used for addressing the requirements?
```
## Marks: 
```
10 marks
```
## 2. Criteria: AWS System Development
```
- Does the developed AWS system address meet the requirements outlined in the Assignment Description?
```
## Marks:
```
25 marks
```
## Total Marks: Criteria (1 + 2):
```
Total 35 marks