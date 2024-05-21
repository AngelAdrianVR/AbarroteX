const dbName = 'POSDatabase';
let dbVersion;
let db;

const initialStores = [
  {
    name: 'products', keyPath: 'id', indexes: [
      { name: 'name', keyPath: 'name', unique: false },
      { name: 'code', keyPath: 'code', unique: false }
    ]
  },
];

function openDatabase() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(dbName);

    request.onupgradeneeded = event => {
      db = event.target.result;
      dbVersion = db.version;
      initialStores.forEach(store => {
        if (!db.objectStoreNames.contains(store.name)) {
          const objectStore = db.createObjectStore(store.name, { keyPath: store.keyPath });
          store.indexes.forEach(index => {
            objectStore.createIndex(index.name, index.keyPath, { unique: index.unique });
          });
        }
      });
    };

    request.onsuccess = event => {
      db = event.target.result;
      resolve(db);
    };

    request.onerror = event => {
      reject(event.target.errorCode);
      console.error(event.target.error);
    };
  });
}

function ensureObjectStore(storeName, keyPath = 'id', indexes = []) {
  return new Promise((resolve, reject) => {
    if (db.objectStoreNames.contains(storeName)) {
      resolve();
    } else {
      const newVersion = dbVersion + 1;
      db.close();
      const request = indexedDB.open(dbName, newVersion);

      request.onupgradeneeded = event => {
        db = event.target.result;
        dbVersion = newVersion;
        const store = db.createObjectStore(storeName, { keyPath: keyPath });
        indexes.forEach(index => store.createIndex(index.name, index.keyPath, { unique: index.unique || false }));
      };

      request.onsuccess = event => {
        db = event.target.result;
        resolve();
      };

      request.onerror = event => {
        reject(event.target.errorCode);
      };
    }
  });
}

function getAll(storeName) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readonly');
    const store = transaction.objectStore(storeName);
    const request = store.getAll();

    request.onsuccess = () => {
      resolve(request.result);
    };

    request.onerror = () => {
      reject(request.error);
    };
  });
}

function getItemByAttributes(storeName, attributes) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readonly');
    const store = transaction.objectStore(storeName);
    const indexKeys = Object.keys(attributes);
    const results = [];

    let completed = 0;
    indexKeys.forEach(key => {
      const index = store.index(key);
      const request = index.getAll(attributes[key]);

      request.onsuccess = () => {
        results.push(...request.result);
        completed++;
        if (completed === indexKeys.length) {
          const uniqueResults = Array.from(new Set(results.map(item => item.id)))
            .map(id => results.find(item => item.id === id));
          resolve(uniqueResults);
        }
      };

      request.onerror = () => {
        reject(request.error);
      };
    });
  });
}

function addOrUpdateItem(storeName, item) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readwrite');
    const store = transaction.objectStore(storeName);
    const request = store.put(item);

    request.onsuccess = () => {
      resolve();
    };

    request.onerror = () => {
      reject(request.error);
    };
  });
}

function deleteItem(storeName, id) {
  return new Promise((resolve, reject) => {
    const transaction = db.transaction(storeName, 'readwrite');
    const store = transaction.objectStore(storeName);
    const request = store.delete(id);

    request.onsuccess = () => {
      resolve();
    };

    request.onerror = () => {
      reject(request.error);
    };
  });
}

function deleteObjectStore(storeName) {
  return new Promise((resolve, reject) => {
    if (!db.objectStoreNames.contains(storeName)) {
      resolve();
      return;
    }

    const newVersion = dbVersion + 1;
    db.close();
    const request = indexedDB.open(dbName, newVersion);

    request.onupgradeneeded = event => {
      db = event.target.result;
      db.deleteObjectStore(storeName);
      dbVersion = newVersion;
    };

    request.onsuccess = event => {
      db = event.target.result;
      resolve();
    };

    request.onerror = event => {
      reject(event.target.errorCode);
    };
  });
}

function closeDatabaseConnection() {
  if (db) {
    db.close();
    db = null;
  }
}

function deleteDatabase() {
  return new Promise((resolve, reject) => {
    closeDatabaseConnection();
    const request = indexedDB.deleteDatabase(dbName);

    request.onsuccess = () => {
      console.log('Base de datos eliminada con éxito.');
      resolve();
    };

    request.onerror = (event) => {
      reject(event.target.error);
    };

    request.onblocked = () => {
      console.warn('Eliminación de la base de datos bloqueada por otra pestaña.');
      // Puedes notificar al usuario aquí o intentar de nuevo después de un tiempo.
    };
  });
}

function clearObjectStore(storeName) {
  return new Promise((resolve, reject) => {
    if (!db.objectStoreNames.contains(storeName)) {
      resolve();
      return;
    }

    const transaction = db.transaction(storeName, 'readwrite');
    const store = transaction.objectStore(storeName);
    const request = store.clear();

    request.onsuccess = () => {
      resolve();
    };

    request.onerror = () => {
      reject(request.error);
    };
  });
}

function tableExists(storeName) {
  return db.objectStoreNames.contains(storeName);
}

export {
  openDatabase,
  ensureObjectStore,
  getAll,
  getItemByAttributes,
  addOrUpdateItem,
  deleteItem,
  deleteObjectStore,
  deleteDatabase,
  clearObjectStore,
  tableExists
};
