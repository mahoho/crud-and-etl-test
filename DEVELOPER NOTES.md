## Explanation of Task 1

I intentionally made Task 1 more complicated than required, and here's why:

1. **Multiple Data Sources**:  
   In real-world scenarios, we often have different data sources, each providing specific types of files. In this example, I referred to the source as "hotels."

2. **Raw Data Loading**:  
   As a first step, data is loaded into a `Raw` table without any modifications (except for standardizing column names to match RDBMS conventions, which simplifies further processing). Each source has its own separate table.
    - **No Truncation**: The table is not truncated, and uploads are identified by a unique execution ID.
    - **Historical Tracking**: This allows us to track how external data has changed over time.
    - **Reprocessing**: We can also reprocess raw data if the ETL logic changes, without needing to access external APIs, which might have restrictions like time limitations.

3. **Core Data Processing**:  
   In the second step, we process each upload and load the cleaned data into the core tables.
    - **Matching Logic**: In real cases, there could be complex rules for matching data from different sources into a unified format. However, for simplicity in this example, we just match hotels and cities by name.

4. **Progress Tracking**:  
   The progress of each process is tracked in the `raw__execution_process` table. This provides full visibility into what’s happening during execution.
    - **Front-End Integration**: In most cases, a front-end interface is also implemented to track the status of these processes, especially if the raw data files are uploaded manually.

5. **ETL Process Flow**:  
   This implementation assumes that the ETL process is triggered through artisan commands. We have two commands:
    - **File -> Raw**: This command loads the raw data.
    - **Raw -> Core**: This command processes the raw data and moves it to the core tables.  
      This separation allows for running the two steps independently, giving flexibility to debug or reprocess specific data as needed.

---

## Explanation of Task 2

For the CRUD operations in Task 2, I intentionally used a single `save` method for both creating and updating records. Here’s why:

- **Simplified Front-End Logic**:  
  In my experience, unifying these actions simplifies the front-end logic since both creation and updating often share the same validation and processing rules.

- **Reduced Boilerplate**:  
  Combining the two actions also reduces redundant code on the backend. In most cases, there’s no need for separate methods for creation and saving, as they follow the same workflow.

- **Consistency in HTTP Methods**:  
  For the same reason, I opted to use only `GET` and `POST` methods. This keeps the API structure simple and avoids unnecessary complexity.
